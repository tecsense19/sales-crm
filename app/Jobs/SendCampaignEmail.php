<?php

namespace App\Jobs;

use App\Mail\CampaignMail;
use App\Models\Campaign;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendCampaignEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $campaign;
    protected $recipient;

    /**
     * Create a new job instance.
     */
    public function __construct(Campaign $campaign, $recipient)
    {
        $this->campaign = $campaign;
        $this->recipient = $recipient;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            Mail::to($this->recipient['email'])->send(
                new CampaignMail($this->campaign->subject, $this->campaign->body, $this->recipient)
            );

            $this->campaign->increment('sent_count');
        } catch (\Exception $e) {
            \Log::error("Failed to send campaign {$this->campaign->id} to {$this->recipient['email']}: " . $e->getMessage());
            $this->campaign->increment('failed_count');
            
            // If it fails, we don't change the main campaign status to 'Failed' 
            // because other emails might succeed. 
        }
    }
}
