<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\User;
use Illuminate\Http\Request;

class KanbanController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        if (!$user) {
            return redirect()->route('signin');
        }

        $statuses = [
            'New',
            'Interested',
            'Contacted',
            'In Progress',
            'Follow Up',
            'On Hold',
            'Converted',
            'Closed Won',
            'Closed Lost',
            'Not Interested'
        ];

        // Fetch users list for filter (for admins/managers)
        $users = [];
        if ($user->role !== 'employee') {
            $users = User::all();
        }

        $locationsQuery = Client::query();
        if ($user->role === 'employee') {
            $locationsQuery->where('assigned_to', $user->id);
        }
        $locations = $locationsQuery->whereNotNull('location')
            ->where('location', '!=', '')
            ->distinct()
            ->orderBy('location')
            ->pluck('location')
            ->toArray();

        return view('pages.kanban.index', [
            'title' => 'Client Board',
            'statuses' => $statuses,
            'users' => $users,
            'locations' => $locations
        ]);
    }

    private function buildFilterQuery(Request $request, $user)
    {
        $query = Client::query();

        // RBAC: Employee sees only their own assigned clients
        if ($user->role === 'employee') {
            $query->where('assigned_to', $user->id);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                  ->orWhere('email', 'like', "%$search%")
                  ->orWhere('location', 'like', "%$search%")
                  ->orWhere('technology', 'like', "%$search%");
            });
        }

        // Filter by Technology
        if ($request->filled('technology')) {
            $query->where('technology', 'like', '%' . $request->technology . '%');
        }

        // Filter by Location
        if ($request->filled('location')) {
            $query->where('location', $request->location);
        }

        // Filter by Assigned User
        if ($request->filled('assigned_to')) {
            $query->where('assigned_to', $request->assigned_to);
        }

        // Filter by Next Follow-up Date range
        if ($request->filled('date_start') && $request->filled('date_end')) {
            $query->whereBetween('next_followup_date', [$request->date_start, $request->date_end]);
        }

        return $query;
    }

    private function transformClient(Client $c)
    {
        return [
            'id'                  => $c->id,
            'name'                => $c->name,
            'email'               => $c->email ?? '',
            'mobile_no'           => $c->mobile_no ?? '',
            'website'             => $c->website ?? '',
            'linkedin'            => $c->linkedin ?? '',
            'facebook'            => $c->facebook ?? '',
            'instagram'           => $c->instagram ?? '',
            'youtube'             => $c->youtube ?? '',
            'x'                   => $c->x ?? '',
            'telegram'            => $c->telegram ?? '',
            'whatsapp'            => $c->whatsapp ?? '',
            'teams'               => $c->teams ?? '',
            'source_url'          => $c->source_url ?? '',
            'project_link'        => $c->project_link ?? '',
            'technology'          => $c->technology ?? '',
            'location'            => $c->location ?? '',
            'status'              => $c->status,
            'is_overdue'          => $c->isFollowUpOverdue(),
            'last_contacted'      => $c->last_contacted_date ? $c->last_contacted_date->format('d M Y') : '',
            'last_contacted_raw'  => $c->last_contacted_date ? $c->last_contacted_date->format('Y-m-d') : '',
            'next_followup'       => $c->next_follow_up_date ? $c->next_follow_up_date->format('d M Y') : '',
            'next_followup_raw'   => $c->next_follow_up_date ? $c->next_follow_up_date->format('Y-m-d') : '',
            'follow_up_days'      => $c->follow_up_days ?? '',
            'notes'               => $c->notes ?? '',
            'assigned_user'       => $c->assignedUser ? $c->assignedUser->name : 'Unassigned',
            'assigned_to'         => $c->assigned_to,
            'initials'            => collect(explode(' ', $c->name))->map(fn($n) => mb_substr($n,0,1))->take(2)->join(''),
            'show_url'            => route('clients.show', $c),
            'edit_url'            => route('clients.edit', $c),
            'quick_update_url'    => route('kanban.quick-update', $c),
        ];
    }

    public function getBoardData(Request $request)
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $statuses = [
            'New',
            'Interested',
            'Contacted',
            'In Progress',
            'Follow Up',
            'On Hold',
            'Converted',
            'Closed Won',
            'Closed Lost',
            'Not Interested'
        ];

        // 1. Get counts per status with the applied filters
        $countsQuery = $this->buildFilterQuery($request, $user);
        $statusCounts = $countsQuery->selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        // Ensure all statuses have a count (default to 0)
        $counts = [];
        foreach ($statuses as $status) {
            $counts[$status] = $statusCounts[$status] ?? 0;
        }

        // 2. Fetch the first 15 cards for each status
        $cards = [];
        $hasMore = [];

        foreach ($statuses as $status) {
            $statusQuery = $this->buildFilterQuery($request, $user)
                ->where('status', $status)
                ->with('assignedUser')
                ->latest()
                ->paginate(15, ['*'], 'page', 1);

            $cards[$status] = collect($statusQuery->items())->map(function($c) {
                return $this->transformClient($c);
            })->toArray();

            $hasMore[$status] = $statusQuery->hasMorePages();
        }

        return response()->json([
            'counts' => $counts,
            'cards' => $cards,
            'has_more' => $hasMore,
        ]);
    }

    public function getColumnCards(Request $request)
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $status = $request->status;
        if (!$status) {
            return response()->json(['error' => 'Status is required'], 400);
        }

        $query = $this->buildFilterQuery($request, $user)
            ->where('status', $status)
            ->with('assignedUser')
            ->latest();

        $paginator = $query->paginate($request->integer('per_page', 15));

        $cards = collect($paginator->items())->map(function($c) {
            return $this->transformClient($c);
        })->toArray();

        return response()->json([
            'cards' => $cards,
            'has_more' => $paginator->hasMorePages(),
        ]);
    }

    public function updateStatus(Request $request)
    {
        $request->validate([
            'client_id' => 'required|exists:clients,id',
            'status' => 'required|string|in:New,Interested,Contacted,In Progress,Follow Up,On Hold,Converted,Closed Won,Closed Lost,Not Interested',
        ]);

        $client = Client::findOrFail($request->client_id);
        $user = auth()->user();

        // RBAC Check
        if ($user->role === 'employee' && $client->assigned_to !== $user->id) {
            return response()->json(['error' => 'Unauthorized action.'], 403);
        }

        $oldStatus = $client->status;
        $client->status = $request->status;

        // Optionally record contact history or update dates if transitioning to contacted/converted
        if ($request->status === 'Contacted' && !$client->last_contacted_date) {
            $client->last_contacted_date = now()->format('Y-m-d');
        }

        $client->save();

        return response()->json([
            'success' => true,
            'message' => "Client '{$client->name}' status updated to {$request->status}.",
            'client' => [
                'id' => $client->id,
                'name' => $client->name,
                'status' => $client->status,
                'old_status' => $oldStatus
            ]
        ]);
    }

    public function quickUpdate(Request $request, Client $client)
    {
        $user = auth()->user();

        if ($user->role === 'employee' && $client->assigned_to !== $user->id) {
            return response()->json(['error' => 'Unauthorized action.'], 403);
        }

        $validated = $request->validate([
            'name'               => 'sometimes|required|string|max:255',
            'email'              => 'sometimes|nullable|email|max:255',
            'mobile_no'          => 'sometimes|nullable|string|max:30',
            'location'           => 'sometimes|nullable|string|max:255',
            'technology'         => 'sometimes|nullable|string|max:255',
            'status'             => 'sometimes|nullable|string|in:New,Interested,Contacted,In Progress,Follow Up,On Hold,Converted,Closed Won,Closed Lost,Not Interested',
            'last_contacted_date'=> 'sometimes|nullable|date',
            'follow_up_days'     => 'sometimes|nullable|integer|min:0',
            'notes'              => 'sometimes|nullable|string',
            'assigned_to'        => 'sometimes|nullable|exists:users,id',
            'website'            => 'sometimes|nullable|string|max:255',
            'linkedin'           => 'sometimes|nullable|string|max:255',
            'facebook'           => 'sometimes|nullable|string|max:255',
            'instagram'          => 'sometimes|nullable|string|max:255',
            'youtube'            => 'sometimes|nullable|string|max:255',
            'x'                  => 'sometimes|nullable|string|max:255',
            'telegram'           => 'sometimes|nullable|string|max:255',
            'whatsapp'           => 'sometimes|nullable|string|max:255',
            'teams'              => 'sometimes|nullable|string|max:255',
            'source_url'         => 'sometimes|nullable|string|max:255',
            'project_link'       => 'sometimes|nullable|string|max:255',
        ]);

        $client->fill($validated)->save();

        return response()->json([
            'success' => true,
            'client'  => [
                'id'               => $client->id,
                'name'             => $client->name,
                'email'            => $client->email,
                'mobile_no'        => $client->mobile_no,
                'location'         => $client->location,
                'technology'       => $client->technology,
                'status'           => $client->status,
                'last_contacted_date' => $client->last_contacted_date?->format('Y-m-d'),
                'follow_up_days'   => $client->follow_up_days,
                'notes'            => $client->notes,
                'assigned_to'      => $client->assigned_to,
                'assigned_user'    => $client->assignedUser?->name ?? 'Unassigned',
                'website'          => $client->website,
                'linkedin'         => $client->linkedin,
                'facebook'         => $client->facebook,
                'instagram'        => $client->instagram,
                'youtube'          => $client->youtube,
                'x'                => $client->x,
                'telegram'         => $client->telegram,
                'whatsapp'         => $client->whatsapp,
                'teams'            => $client->teams,
                'source_url'       => $client->source_url,
                'project_link'     => $client->project_link,
                'next_followup'    => $client->next_follow_up_date?->format('d M Y'),
                'next_followup_raw'=> $client->next_follow_up_date?->format('Y-m-d'),
                'is_overdue'       => $client->isFollowUpOverdue(),
            ]
        ]);
    }
}
