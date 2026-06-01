<?php

namespace App\Console\Commands;

use App\Services\SmtpRotationService;
use Illuminate\Console\Command;

class RetryPendingEmails extends Command
{
    protected $signature   = 'emails:retry-pending';
    protected $description = 'Retry pending emails when SMTP provider limits have reset';

    public function handle(SmtpRotationService $smtpService): void
    {
        $this->info('Checking for pending emails to retry...');
        $smtpService->retryPending();
        $this->info('Done.');
    }
}
