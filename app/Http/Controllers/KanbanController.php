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

        $query = Client::query();

        // RBAC: Employee sees only their own assigned clients
        if ($user->role === 'employee') {
            $query->where('assigned_to', $user->id);
        }

        $clients = $query->with('assignedUser')->latest()->get();

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

        return view('pages.kanban.index', [
            'title' => 'Client Board',
            'clients' => $clients,
            'statuses' => $statuses,
            'users' => $users
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
