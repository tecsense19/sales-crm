<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        if (!$user) {
            return redirect()->route('signin');
        }

        $filter = $request->get('filter', 'month');
        $dateRange = $this->getDateRange($filter);

        $query = Client::query();

        // RBAC
        if ($user->role === 'employee') {
            $query->where('assigned_to', $user->id);
        }

        // Apply Time Filter strictly
        if ($dateRange) {
            $query->where('created_at', '>=', $dateRange['start'])
                  ->where('created_at', '<=', $dateRange['end']);
        }

        // Stats Calculation
        $totalClients = (clone $query)->count();
        // Updated "New Lead" to include more statuses that represent a new contact
        $newLeads = (clone $query)->whereIn('status', ['Interested', 'New', 'Contacted', 'Lead'])->count();
        $activeClients = (clone $query)->whereIn('status', ['Follow Up', 'In Progress', 'On Hold'])->count();
        $closedClients = (clone $query)->whereIn('status', ['Closed Won', 'Converted'])->count();
        
        // Pipeline Distribution
        $statusDistribution = (clone $query)
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->orderBy('total', 'desc')
            ->get();

        // Team Wise Statistics (Admins only)
        $userPerformance = [];
        if ($user->role !== 'employee') {
            $userPerformance = User::withCount(['clients' => function($q) use ($dateRange) {
                    if ($dateRange) {
                        $q->where('created_at', '>=', $dateRange['start'])
                          ->where('created_at', '<=', $dateRange['end']);
                    }
                }])
                ->where('role', 'employee')
                ->get();
        }

        // Lead Trends Data for ApexCharts
        $trends = $this->getLeadTrends($user, $filter, $dateRange);

        return view('pages.dashboard.ecommerce', [
            'title' => 'CRM Dashboard',
            'filter' => $filter,
            'stats' => [
                'new_leads' => $newLeads,
                'total' => $totalClients,
                'active' => $activeClients,
                'closed' => $closedClients,
            ],
            'statusDistribution' => $statusDistribution,
            'userPerformance' => $userPerformance,
            'trends' => $trends
        ]);
    }

    protected function getLeadTrends($user, $filter, $dateRange)
    {
        $query = Client::query();

        if ($user->role === 'employee') {
            $query->where('assigned_to', $user->id);
        }

        if ($filter === 'today') {
            // For Today, show Hourly trends
            $data = $query->select(
                    DB::raw('HOUR(created_at) as hour'), 
                    DB::raw('count(*) as total')
                )
                ->whereDate('created_at', Carbon::today())
                ->groupBy('hour')
                ->orderBy('hour', 'asc')
                ->get();

            return [
                'labels' => $data->pluck('hour')->map(fn($h) => sprintf('%02d:00', $h))->toArray(),
                'values' => $data->pluck('total')->toArray()
            ];
        }

        // For Week/Month, show Daily trends
        $data = $query->select(
                DB::raw('DATE(created_at) as date'), 
                DB::raw('count(*) as total')
            )
            ->where('created_at', '>=', $dateRange['start'])
            ->where('created_at', '<=', $dateRange['end'])
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        return [
            'labels' => $data->pluck('date')->map(fn($d) => Carbon::parse($d)->format('d M'))->toArray(),
            'values' => $data->pluck('total')->toArray()
        ];
    }

    protected function getDateRange($filter)
    {
        return match($filter) {
            'today' => [
                'start' => Carbon::today()->startOfDay()->toDateTimeString(),
                'end' => Carbon::today()->endOfDay()->toDateTimeString()
            ],
            'week' => [
                'start' => Carbon::now()->startOfWeek()->toDateTimeString(),
                'end' => Carbon::now()->endOfWeek()->toDateTimeString()
            ],
            'month' => [
                'start' => Carbon::now()->startOfMonth()->toDateTimeString(),
                'end' => Carbon::now()->endOfMonth()->toDateTimeString()
            ],
            default => [
                'start' => Carbon::now()->startOfMonth()->toDateTimeString(),
                'end' => Carbon::now()->endOfMonth()->toDateTimeString()
            ],
        };
    }
}
