<?php

namespace App\Http\Controllers;

use App\Models\SmtpProvider;
use App\Models\PendingEmail;
use Illuminate\Http\Request;

class SmtpProviderController extends Controller
{
    public function index()
    {
        if (auth()->user()->role !== 'admin') abort(403);

        $providers = SmtpProvider::orderBy('priority')->get();
        $pendingCount = PendingEmail::where('status', 'pending')->count();
        $sentToday = $providers->sum('sent_today');
        $totalCapacity = $providers->where('is_active', true)->sum('daily_limit');

        return view('pages.settings.smtp-providers', compact(
            'providers', 'pendingCount', 'sentToday', 'totalCapacity'
        ));
    }

    public function store(Request $request)
    {
        if (auth()->user()->role !== 'admin') abort(403);

        $validated = $request->validate([
            'name'       => 'required|string|max:255',
            'driver'     => 'required|in:smtp2go,brevo',
            'api_key'    => 'required|string|max:512',
            'from_email' => 'required|email|max:255',
            'from_name'  => 'required|string|max:255',
            'daily_limit'=> 'required|integer|min:1',
            'priority'   => 'required|integer|min:1',
            'is_active'  => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['limit_reset_date'] = today();

        SmtpProvider::create($validated);

        return redirect()->route('smtp-providers.index')
            ->with('success', 'Email provider added successfully.');
    }

    public function update(Request $request, SmtpProvider $smtpProvider)
    {
        if (auth()->user()->role !== 'admin') abort(403);

        $validated = $request->validate([
            'name'       => 'required|string|max:255',
            'driver'     => 'required|in:smtp2go,brevo',
            'api_key'    => 'nullable|string|max:512',
            'from_email' => 'required|email|max:255',
            'from_name'  => 'required|string|max:255',
            'daily_limit'=> 'required|integer|min:1',
            'priority'   => 'required|integer|min:1',
            'is_active'  => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        // Prevent erasing API key if left blank on edit
        if (empty($validated['api_key'])) {
            unset($validated['api_key']);
        }

        $smtpProvider->update($validated);

        return redirect()->route('smtp-providers.index')
            ->with('success', "Provider '{$smtpProvider->name}' updated successfully.");
    }

    public function destroy(SmtpProvider $smtpProvider)
    {
        if (auth()->user()->role !== 'admin') abort(403);

        $smtpProvider->delete();
        return redirect()->route('smtp-providers.index')
            ->with('success', 'Provider removed.');
    }

    public function resetCounter(SmtpProvider $smtpProvider)
    {
        if (auth()->user()->role !== 'admin') abort(403);

        $smtpProvider->update(['sent_today' => 0, 'limit_reset_date' => today()]);
        return back()->with('success', "Counter reset for '{$smtpProvider->name}'.");
    }

    public function toggleActive(SmtpProvider $smtpProvider)
    {
        if (auth()->user()->role !== 'admin') abort(403);

        $smtpProvider->update(['is_active' => !$smtpProvider->is_active]);
        $state = $smtpProvider->fresh()->is_active ? 'enabled' : 'disabled';
        return back()->with('success', "Provider '{$smtpProvider->name}' {$state}.");
    }

    public function pendingEmails()
    {
        if (auth()->user()->role !== 'admin') abort(403);

        $pending = PendingEmail::with('campaign')
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('pages.settings.pending-emails', compact('pending'));
    }

    public function bulkDestroyPending(Request $request)
    {
        if (auth()->user()->role !== 'admin') abort(403);

        $validated = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:pending_emails,id',
        ]);

        try {
            PendingEmail::whereIn('id', $validated['ids'])->delete();
            return redirect()->route('smtp-providers.pending')
                ->with('success', count($validated['ids']) . ' pending emails deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete pending emails: ' . $e->getMessage());
        }
    }
}
