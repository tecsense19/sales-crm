<?php

namespace App\Jobs;

use App\Models\Campaign;
use App\Services\SmtpRotationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

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
        $this->campaign  = $campaign;
        $this->recipient = $recipient;
    }

    /**
     * Execute the job — delegates to SmtpRotationService.
     * If all SMTP providers are at their daily limit, the email is queued as pending.
     */
    public function handle(SmtpRotationService $smtpService): void
    {
        $smtpService->send($this->campaign, $this->recipient);
    }
}
