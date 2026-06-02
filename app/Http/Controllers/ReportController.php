<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $query = Client::query();

        // RBAC
        if ($user->role === 'employee') {
            $query->where('assigned_to', $user->id);
        }

        // Date Range Filter
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        $dateRange = $request->get('date_range');

        if ($dateRange) {
            $parts = explode(' to ', $dateRange);
            if (count($parts) === 2) {
                $startDate = trim($parts[0]);
                $endDate = trim($parts[1]);
            } else {
                $startDate = $endDate = trim($dateRange);
            }
        }

        $startDate = $startDate ?: Carbon::now()->subMonth()->startOfMonth()->toDateString();
        $endDate = $endDate ?: Carbon::now()->toDateString();

        $query->whereBetween('created_at', [
            Carbon::parse($startDate)->startOfDay(),
            Carbon::parse($endDate)->endOfDay()
        ]);

        // General Stats
        $totalClients = (clone $query)->count();
        $statusDistribution = (clone $query)
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->get();

        $technologyDistribution = (clone $query)
            ->whereNotNull('technology')
            ->select('technology', DB::raw('count(*) as total'))
            ->groupBy('technology')
            ->orderBy('total', 'desc')
            ->limit(10)
            ->get();

        $countryDistribution = (clone $query)
            ->whereNotNull('location')
            ->select(DB::raw('TRIM(SUBSTRING_INDEX(location, ",", -1)) as country'), DB::raw('count(*) as total'))
            ->groupBy('country')
            ->orderBy('total', 'desc')
            ->get()
            ->map(function($item) {
                $item->map_name = match(trim($item->country)) {
                    'UK' => 'United Kingdom',
                    'USA' => 'United States',
                    'UAE' => 'United Arab Emirates',
                    'South Korea' => 'South Korea', // Google usually handles this, but let's be sure
                    default => $item->country
                };
                return $item;
            });

        return view('pages.reports.index', [
            'title' => 'General Reports',
            'totalClients' => $totalClients,
            'statusDistribution' => $statusDistribution,
            'technologyDistribution' => $technologyDistribution,
            'countryDistribution' => $countryDistribution,
            'startDate' => $startDate,
            'endDate' => $endDate,
        ]);
    }

    public function teamWise(Request $request)
    {
        $user = auth()->user();
        
        // Only admins should see full team reports
        if ($user->role === 'employee') {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access.');
        }

        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        $dateRange = $request->get('date_range');

        if ($dateRange) {
            $parts = explode(' to ', $dateRange);
            if (count($parts) === 2) {
                $startDate = trim($parts[0]);
                $endDate = trim($parts[1]);
            } else {
                $startDate = $endDate = trim($dateRange);
            }
        }

        $startDate = $startDate ?: Carbon::now()->subMonth()->startOfMonth()->toDateString();
        $endDate = $endDate ?: Carbon::now()->toDateString();
        $employeeId = $request->get('employee_id');

        // Grouping by Assigned User
        $teamStatsQuery = Client::query()
            ->join('users', 'clients.assigned_to', '=', 'users.id')
            ->whereBetween('clients.created_at', [
                Carbon::parse($startDate)->startOfDay(),
                Carbon::parse($endDate)->endOfDay()
            ]);

        if ($employeeId) {
            $teamStatsQuery->where('users.id', $employeeId);
        }

        $teamStats = $teamStatsQuery
            ->select('users.name as teams', 
                DB::raw('count(*) as total_clients'),
                DB::raw('SUM(CASE WHEN clients.status IN ("Closed Won", "Converted") THEN 1 ELSE 0 END) as closed_won'),
                DB::raw('SUM(CASE WHEN clients.status IN ("Interested", "New", "Lead") THEN 1 ELSE 0 END) as new_leads')
            )
            ->groupBy('users.id', 'users.name')
            ->paginate(10)
            ->withQueryString();

        // Performance by Assigned User (Team members)
        $userPerformanceQuery = User::withCount(['clients' => function($q) use ($startDate, $endDate) {
                $q->whereBetween('created_at', [
                    Carbon::parse($startDate)->startOfDay(),
                    Carbon::parse($endDate)->endOfDay()
                ]);
            }]);

        if ($employeeId) {
            $userPerformanceQuery->where('id', $employeeId);
        }

        $userPerformance = $userPerformanceQuery->get();
        $employees = User::orderBy('name')->get();

        return view('pages.reports.team-wise', [
            'title' => 'Team Wise Reports',
            'teamStats' => $teamStats,
            'userPerformance' => $userPerformance,
            'employees' => $employees,
            'selectedEmployeeId' => $employeeId,
            'startDate' => $startDate,
            'endDate' => $endDate,
        ]);
    }
}
