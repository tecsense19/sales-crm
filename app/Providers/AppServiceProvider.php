<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Event;

use Illuminate\Support\Facades\Mail;
use App\Mail\Transport\RotationTransport;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register custom rotation mailer driver
        Mail::extend('rotation', function (array $config) {
            return new RotationTransport();
        });

        // Coment: Background Queue Worker Trigger Logic
        // $triggerWorker = function ($notifiable = null) {
            // Coment: Use a short cache lock to prevent spawning multiple workers simultaneously for the same batch
            // if (Cache::lock('import_worker_trigger', 5)->get()) {
            //     Log::debug('Queue worker triggered manually via event.');

            //     $php = PHP_BINARY;
            //     $artisan = base_path('artisan');

            //     if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                    // Coment: Windows background command
                    // \Illuminate\Support\Facades\Log::info('TailAdmin: Triggering background queue worker (queue:work)');
                    // pclose(popen("start /B {$php} \"{$artisan}\" queue:work --stop-when-empty --tries=1", "r"));
                // } else {
                    // Coment: Linux background command
        //             \Illuminate\Support\Facades\Log::info('TailAdmin: Triggering background queue worker (queue:work)');
        //             exec("{$php} \"{$artisan}\" queue:work --stop-when-empty --tries=1 > /dev/null 2>&1 &");
        //         }
        //     } else {
        //         \Illuminate\Support\Facades\Log::debug('TailAdmin: Worker trigger skipped: A worker was recently started and is likely already processing the queue.');
        //     }
        // };

        // --- 2. Trigger Scheduler (Poor Man's Cron) ---
        // This allows scheduled tasks (like campaigns) to run even without a system cron job.
        // It runs once every minute (using a cache lock).
        // $triggerScheduler = function () {
        //     if (Cache::lock('scheduler_trigger', 55)->get()) {
        //         \Illuminate\Support\Facades\Log::info('TailAdmin: Triggering background scheduler (schedule:run)');
        //         $php = PHP_BINARY;
        //         $artisan = base_path('artisan');

        //         if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
        //             pclose(popen("start /B {$php} \"{$artisan}\" schedule:run", "r"));
        //         } else {
        //             exec("{$php} \"{$artisan}\" schedule:run > /dev/null 2>&1 &");
        //         }
        //     }
        // };

        // Fire the scheduler on every request
        // $triggerScheduler();

        // --- 3. Queue Worker Listeners ---
        // Trigger for ANY job pushed to the queue
        // Event::listen(\Illuminate\Queue\Events\JobQueued::class, function ($event) use ($triggerWorker) {
        //     $triggerWorker((object)['id' => 'QueuedJob:' . ($event->id ?? 'unknown')]);
        // });

        // Also trigger when a notification is being sent
        // Event::listen(\Illuminate\Notifications\Events\NotificationSending::class, function ($event) use ($triggerWorker) {
        //     $triggerWorker($event->notification);
        // });

        // Trigger when a database notification is created
        // \Illuminate\Notifications\DatabaseNotification::created($triggerWorker);
    }
}
