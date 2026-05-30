<?php

namespace App\Console\Commands;

use App\Jobs\ProcessCampaign;
use App\Models\Campaign;
use Illuminate\Console\Command;

class SendScheduledCampaigns extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'campaigns:send-scheduled';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Find and process campaigns that are scheduled to be sent now';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for scheduled campaigns...');

        $campaigns = Campaign::where('status', 'Scheduled')
            ->where('scheduled_at', '<=', now())
            ->get();

        if ($campaigns->isEmpty()) {
            $this->info('No campaigns to process.');
            return;
        }

        foreach ($campaigns as $campaign) {
            $this->info("Dispatching campaign: {$campaign->name}");
            ProcessCampaign::dispatch($campaign);
        }

        $this->info('All scheduled campaigns have been dispatched to the queue.');
    }
}
