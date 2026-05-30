<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\Client;
use Illuminate\Http\Request;

class CampaignController extends Controller
{
    public function index()
    {
        $campaigns = Campaign::latest()->paginate(10);
        
        $stats = [
            'total_sent' => Campaign::sum('sent_count'),
            'total_failed' => Campaign::sum('failed_count'),
            'campaigns_count' => Campaign::count(),
            'total_clients' => Client::count(),
        ];

        return view('pages.campaigns.index', compact('campaigns', 'stats'));
    }

    public function create()
    {
        $clients = Client::orderBy('name')->get();
        return view('pages.campaigns.create', compact('clients'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'subject' => 'required|string',
            'content' => 'required|string',
            'target_status' => 'required|string',
            'scheduled_at' => 'nullable|string',
            'selected_clients' => 'nullable|array',
            'external_file' => 'nullable|mimes:csv,xlsx,xls',
        ]);

        $validated['body'] = $validated['content'];
        unset($validated['content']);

        // Handle scheduled_at format (d/m/Y h:i A)
        if (!empty($validated['scheduled_at'])) {
            try {
                $validated['scheduled_at'] = \Carbon\Carbon::createFromFormat('d/m/Y h:i A', $validated['scheduled_at']);
            } catch (\Exception $e) {
                try {
                    $validated['scheduled_at'] = \Carbon\Carbon::parse($validated['scheduled_at']);
                } catch (\Exception $e2) {
                    return back()->withInput()->with('error', 'Invalid date format. Please use d/m/Y h:i A.');
                }
            }
        }

        // Handle external file
        if ($request->hasFile('external_file')) {
            try {
                $rows = \Maatwebsite\Excel\Facades\Excel::toArray([], $request->file('external_file'));
                $emails = [];
                if (!empty($rows[0])) {
                    foreach ($rows[0] as $row) {
                        // Look for email in any column
                        foreach ($row as $cell) {
                            if (filter_var($cell, FILTER_VALIDATE_EMAIL)) {
                                $emails[] = $cell;
                            }
                        }
                    }
                }
                $validated['external_emails'] = array_unique($emails);
            } catch (\Exception $e) {
                return back()->withInput()->with('error', 'Failed to parse external file: ' . $e->getMessage());
            }
        }

        try {
            $campaign = Campaign::create($validated);
            
            if ($campaign->scheduled_at) {
                $campaign->update(['status' => 'Scheduled']);
            }
            
            return redirect()->route('campaigns.index')->with('success', 'Campaign created successfully.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Failed to create campaign: ' . $e->getMessage());
        }
    }

    public function show(Campaign $campaign)
    {
        return view('pages.campaigns.show', compact('campaign'));
    }

    public function edit(Campaign $campaign)
    {
        $clients = Client::orderBy('name')->get();
        return view('pages.campaigns.edit', compact('campaign', 'clients'));
    }

    public function update(Request $request, Campaign $campaign)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'subject' => 'required|string',
            'content' => 'required|string',
            'target_status' => 'required|string',
            'scheduled_at' => 'nullable|string',
            'selected_clients' => 'nullable|array',
            'external_file' => 'nullable|mimes:csv,xlsx,xls',
        ]);

        $validated['body'] = $validated['content'];
        unset($validated['content']);

        // Handle scheduled_at format (d/m/Y h:i A)
        if (!empty($validated['scheduled_at'])) {
            try {
                $validated['scheduled_at'] = \Carbon\Carbon::createFromFormat('d/m/Y h:i A', $validated['scheduled_at']);
            } catch (\Exception $e) {
                try {
                    $validated['scheduled_at'] = \Carbon\Carbon::parse($validated['scheduled_at']);
                } catch (\Exception $e2) {
                    return back()->withInput()->with('error', 'Invalid date format. Please use d/m/Y h:i A.');
                }
            }
        }

        if ($request->hasFile('external_file')) {
            try {
                $rows = \Maatwebsite\Excel\Facades\Excel::toArray([], $request->file('external_file'));
                $emails = [];
                if (!empty($rows[0])) {
                    foreach ($rows[0] as $row) {
                        foreach ($row as $cell) {
                            if (filter_var($cell, FILTER_VALIDATE_EMAIL)) {
                                $emails[] = $cell;
                            }
                        }
                    }
                }
                $validated['external_emails'] = array_unique($emails);
            } catch (\Exception $e) {
                return back()->withInput()->with('error', 'Failed to parse external file: ' . $e->getMessage());
            }
        }

        try {
            $campaign->update($validated);

            if ($campaign->scheduled_at && $campaign->status === 'Draft') {
                $campaign->update(['status' => 'Scheduled']);
            }

            return redirect()->route('campaigns.index')->with('success', 'Campaign updated successfully.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Failed to update campaign: ' . $e->getMessage());
        }
    }

    public function destroy(Campaign $campaign)
    {
        try {
            $campaign->delete();
            return redirect()->route('campaigns.index')->with('success', 'Campaign deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete campaign: ' . $e->getMessage());
        }
    }

    public function bulkDestroy(Request $request)
    {
        $validated = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:campaigns,id',
        ]);

        try {
            Campaign::whereIn('id', $validated['ids'])->delete();
            return redirect()->route('campaigns.index')->with('success', count($validated['ids']) . ' campaigns deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete campaigns: ' . $e->getMessage());
        }
    }

    public function sendNow(Campaign $campaign)
    {
        try {
            if ($campaign->status === 'Sent' || $campaign->status === 'Processing') {
                return back()->with('error', 'This campaign is already being processed or has been sent.');
            }

            \App\Jobs\ProcessCampaign::dispatch($campaign);

            return back()->with('success', 'Campaign processing started successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to start campaign: ' . $e->getMessage());
        }
    }

    public function export()
    {
        $campaigns = Campaign::all();
        $columns = [
            'name' => 'Campaign Name',
            'subject' => 'Subject',
            'status' => 'Status',
            'sent_count' => 'Sent Count',
            'failed_count' => 'Failed Count',
            'scheduled_at' => 'Scheduled At',
            'created_at' => 'Created At',
        ];

        return \App\Helpers\CsvExporter::export($campaigns, 'campaigns_export_' . date('Y-m-d') . '.csv', $columns);
    }
}
