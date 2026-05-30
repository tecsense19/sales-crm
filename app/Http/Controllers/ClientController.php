<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\User;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function index(Request $request)
    {
        $query = Client::query();

        // RBAC: Employee sees only their own clients
        if (auth()->check() && auth()->user()->role === 'employee') {
            $query->where('assigned_to', auth()->id());
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

        // Filter by Status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $clients = $query->with('assignedUser')->latest()->paginate(10);

        return view('pages.clients.index', compact('clients'));
    }

    public function create()
    {
        if (!auth()->check()) {
            abort(403, 'Unauthorized action.');
        }

        $users = User::all();
        return view('pages.clients.create', compact('users'));
    }

    public function store(Request $request)
    {
        if (!auth()->check()) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email',
            'mobile_no' => 'nullable|string',
            'website' => 'nullable|string',
            'project_link' => 'nullable|string',
            'location' => 'nullable|string',
            'technology' => 'nullable|string',
            'status' => 'required|string',
            'date_added' => 'nullable|date',
            'last_contacted_date' => 'nullable|date',
            'follow_up_days' => 'nullable|integer',
            'assigned_to' => 'nullable|exists:users,id',
            'notes' => 'nullable|string',
            'linkedin' => 'nullable|string',
            'facebook' => 'nullable|string',
            'instagram' => 'nullable|string',
            'youtube' => 'nullable|string',
            'x' => 'nullable|string',
            'telegram' => 'nullable|string',
            'whatsapp' => 'nullable|string',
            'teams' => 'nullable|string',
            'source_url' => 'nullable|string',
            'next_followup_date' => 'nullable|date',
        ]);

        // Auto-assign to current user if employee or if not specified
        if (auth()->user()->role === 'employee' || empty($validated['assigned_to'])) {
            $validated['assigned_to'] = auth()->id();
        }

        try {
            $client = null;
            \Illuminate\Support\Facades\DB::transaction(function () use ($validated, &$client) {
                $client = Client::create($validated);
            });

            if ($request->wantsJson()) {
                $client->load('assignedUser');
                return response()->json([
                    'success' => true,
                    'message' => 'Client created successfully.',
                    'client' => [
                        'id'                  => $client->id,
                        'name'                => $client->name,
                        'email'               => $client->email ?? '',
                        'mobile_no'           => $client->mobile_no ?? '',
                        'website'             => $client->website ?? '',
                        'linkedin'            => $client->linkedin ?? '',
                        'facebook'            => $client->facebook ?? '',
                        'instagram'           => $client->instagram ?? '',
                        'youtube'             => $client->youtube ?? '',
                        'x'                   => $client->x ?? '',
                        'telegram'            => $client->telegram ?? '',
                        'whatsapp'            => $client->whatsapp ?? '',
                        'teams'               => $client->teams ?? '',
                        'source_url'          => $client->source_url ?? '',
                        'project_link'        => $client->project_link ?? '',
                        'technology'          => $client->technology ?? '',
                        'location'            => $client->location ?? '',
                        'status'              => $client->status,
                        'is_overdue'          => $client->isFollowUpOverdue(),
                        'last_contacted'      => $client->last_contacted_date ? $client->last_contacted_date->format('d M Y') : '',
                        'last_contacted_raw'  => $client->last_contacted_date ? $client->last_contacted_date->format('Y-m-d') : '',
                        'next_followup'       => $client->next_follow_up_date ? $client->next_follow_up_date->format('d M Y') : '',
                        'next_followup_raw'   => $client->next_follow_up_date ? $client->next_follow_up_date->format('Y-m-d') : '',
                        'follow_up_days'      => $client->follow_up_days ?? '',
                        'notes'               => $client->notes ?? '',
                        'assigned_user'       => $client->assignedUser ? $client->assignedUser->name : 'Unassigned',
                        'assigned_to'         => $client->assigned_to,
                        'initials'            => collect(explode(' ', $client->name))->map(fn($n) => mb_substr($n,0,1))->take(2)->join(''),
                        'show_url'            => route('clients.show', $client),
                        'edit_url'            => route('clients.edit', $client),
                        'quick_update_url'    => route('kanban.quick-update', $client),
                    ]
                ]);
            }

            return redirect()->route('clients.index')->with('success', 'Client created successfully.');
        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to create client: ' . $e->getMessage()
                ], 500);
            }
            return back()->withInput()->with('error', 'Failed to create client: ' . $e->getMessage());
        }
    }

    public function show(Client $client)
    {
        // RBAC
        if (!auth()->check() || (auth()->user()->role === 'employee' && $client->assigned_to !== auth()->id())) {
            abort(403);
        }

        return view('pages.clients.show', compact('client'));
    }

    public function edit(Client $client)
    {
        // Employees can only edit their assigned clients
        if (!auth()->check() || (auth()->user()->role === 'employee' && $client->assigned_to !== auth()->id())) {
            abort(403);
        }

        $users = User::all();
        return view('pages.clients.edit', compact('client', 'users'));
    }

    public function update(Request $request, Client $client)
    {
        // Employees can only update their assigned clients
        if (!auth()->check() || (auth()->user()->role === 'employee' && $client->assigned_to !== auth()->id())) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email',
            'mobile_no' => 'nullable|string',
            'website' => 'nullable|string',
            'project_link' => 'nullable|string',
            'location' => 'nullable|string',
            'technology' => 'nullable|string',
            'status' => 'required|string',
            'date_added' => 'nullable|date',
            'last_contacted_date' => 'nullable|date',
            'follow_up_days' => 'nullable|integer',
            'assigned_to' => 'nullable|exists:users,id',
            'notes' => 'nullable|string',
            'linkedin' => 'nullable|string',
            'facebook' => 'nullable|string',
            'instagram' => 'nullable|string',
            'youtube' => 'nullable|string',
            'x' => 'nullable|string',
            'telegram' => 'nullable|string',
            'whatsapp' => 'nullable|string',
            'teams' => 'nullable|string',
            'source_url' => 'nullable|string',
            'next_followup_date' => 'nullable|date',
        ]);

        try {
            \Illuminate\Support\Facades\DB::transaction(function () use ($client, $validated) {
                $client->update($validated);
            });

            return redirect()->route('clients.index')->with('success', 'Client updated successfully.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Failed to update client: ' . $e->getMessage());
        }
    }

    public function destroy(Client $client)
    {
        if (!auth()->check() || auth()->user()->role !== 'admin') {
            abort(403);
        }

        try {
            \Illuminate\Support\Facades\DB::transaction(function () use ($client) {
                $client->delete();
            });
            return redirect()->route('clients.index')->with('success', 'Client deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete client: ' . $e->getMessage());
        }
    }

    public function bulkDestroy(Request $request)
    {
        if (!auth()->check() || auth()->user()->role !== 'admin') {
            abort(403);
        }

        $validated = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:clients,id',
        ]);

        try {
            Client::whereIn('id', $validated['ids'])->delete();
            return redirect()->route('clients.index')->with('success', count($validated['ids']) . ' clients deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete clients: ' . $e->getMessage());
        }
    }

    public function export()
    {
        $clients = Client::all();
        $columns = [
            'name' => 'Client Name',
            'email' => 'Email',
            'mobile_no' => 'Mobile No',
            'technology' => 'Technology',
            'status' => 'Status',
            'last_contacted_date' => 'Last Contacted',
            'next_follow_up_date' => 'Next Followup',
        ];

        return \App\Helpers\CsvExporter::export($clients, 'clients_export_' . date('Y-m-d') . '.csv', $columns);
    }
}
