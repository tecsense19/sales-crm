<?php

namespace App\Jobs;

use App\Models\Campaign;
use App\Models\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessCampaign implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $campaign;

    /**
     * Create a new job instance.
     */
    public function __construct(Campaign $campaign)
    {
        $this->campaign = $campaign;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Update status to processing
        $this->campaign->update(['status' => 'Processing']);

        // 1. Process CRM Clients using Chunking for memory stability (Handles 10,000+ easily)
        if ($this->campaign->target_status !== 'none') {
            $query = Client::whereNotNull('email');

            if ($this->campaign->target_status === 'custom') {
                $query->whereIn('id', $this->campaign->selected_clients ?? []);
            } elseif ($this->campaign->target_status !== 'all') {
                $query->where('status', $this->campaign->target_status);
            }

            $query->chunk(200, function ($clients) {
                foreach ($clients as $client) {
                    SendCampaignEmail::dispatch($this->campaign, [
                        'id' => $client->id,
                        'name' => $client->name,
                        'email' => $client->email,
                        'location' => $client->location,
                        'type' => 'crm'
                    ]);
                }
            });
        }

        // 2. Process External Emails (already in memory from campaign record)
        if (!empty($this->campaign->external_emails)) {
            foreach ($this->campaign->external_emails as $recipient) {
                if (is_array($recipient)) {
                    SendCampaignEmail::dispatch($this->campaign, [
                        'id' => null,
                        'name' => $recipient['name'] ?? 'Recipient',
                        'email' => $recipient['email'],
                        'location' => $recipient['location'] ?? null,
                        'type' => 'external'
                    ]);
                } else {
                    SendCampaignEmail::dispatch($this->campaign, [
                        'id' => null,
                        'name' => 'Recipient',
                        'email' => $recipient,
                        'location' => null,
                        'type' => 'external'
                    ]);
                }
            }
        }

        // Mark as sent when all jobs are handed off to the queue
        $this->campaign->update(['status' => 'Sent']);
    }
}
