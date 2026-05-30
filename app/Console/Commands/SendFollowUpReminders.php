<?php

namespace App\Console\Commands;

use App\Mail\FollowUpReminderMail;
use App\Mail\TeamFollowUpReminderMail;
use App\Models\Client;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendFollowUpReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clients:send-followup-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Find clients due/overdue for follow-up and send daily reminders to admins and team members';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for due and overdue client follow-ups...');

        $today = today();
        $twoDaysLater = $today->copy()->addDays(2);

        // 1. Upcoming Reminders: next_followup_date is between today and today+2 days, and not closed
        $upcoming = Client::whereBetween('next_followup_date', [$today, $twoDaysLater])
            ->whereNotIn('status', ['Closed Won', 'Closed Lost'])
            ->with('assignedUser')
            ->orderBy('next_followup_date', 'asc')
            ->get();

        // 2. Overdue: next_followup_date is in the past, and not closed
        $overdue = Client::whereDate('next_followup_date', '<', $today)
            ->whereNotIn('status', ['Closed Won', 'Closed Lost'])
            ->with('assignedUser')
            ->orderBy('next_followup_date', 'asc')
            ->get();

        if ($upcoming->isEmpty() && $overdue->isEmpty()) {
            $this->info('No upcoming (next 2 days) or overdue follow-ups. Skipping email reminders.');
            return;
        }

        // --- PART 1: SEND DIGEST EMAIL TO ALL ADMINS ---
        $admins = User::where('role', 'admin')->get();
        if ($admins->isNotEmpty()) {
            $this->info("Found {$upcoming->count()} upcoming, {$overdue->count()} overdue clients. Sending digest to {$admins->count()} admins...");
            foreach ($admins as $admin) {
                try {
                    Mail::to($admin->email)->send(new FollowUpReminderMail($upcoming, $overdue));
                    $this->info("Digest sent to admin: {$admin->email}");
                } catch (\Exception $e) {
                    $this->error("Failed to send digest to {$admin->email}: " . $e->getMessage());
                }
            }
        } else {
            $this->warn('No administrators found to notify.');
        }

        // --- PART 2: SEND INDIVIDUAL OVERDUE REMINDERS TO ASSIGNED TEAM MEMBERS ---
        $assignedOverdueGroups = $overdue->filter(function ($client) {
            return $client->assigned_to !== null && $client->assignedUser !== null;
        })->groupBy('assigned_to');

        if ($assignedOverdueGroups->isNotEmpty()) {
            $this->info("Processing personalized overdue reminders for {$assignedOverdueGroups->count()} assigned team members...");
            foreach ($assignedOverdueGroups as $userId => $clients) {
                $assignedUser = $clients->first()->assignedUser;
                try {
                    Mail::to($assignedUser->email)->send(new TeamFollowUpReminderMail($clients));
                    $this->info("Overdue reminder sent to team member: {$assignedUser->email} ({$clients->count()} clients)");
                } catch (\Exception $e) {
                    $this->error("Failed to send overdue reminder to team member {$assignedUser->email}: " . $e->getMessage());
                }
            }
        } else {
            $this->info('No assigned overdue clients found to notify team members.');
        }

        $this->info('Follow-up reminders processed successfully.');
    }
}
