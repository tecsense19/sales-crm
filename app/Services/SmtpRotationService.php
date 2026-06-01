<?php

namespace App\Services;

use App\Models\SmtpProvider;
use App\Models\PendingEmail;
use App\Models\Campaign;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmtpRotationService
{
    /**
     * Send a campaign email using smart API rotation.
     * Auto-switches between Brevo and SMTP2GO based on daily limits.
     * If all providers are exhausted, queues email as pending.
     *
     * @return string  'sent' | 'pending'
     */
    public function send(Campaign $campaign, array $recipient): string
    {
        $provider = SmtpProvider::getAvailable();

        if (!$provider) {
            // All providers exhausted — save to pending_emails for later
            PendingEmail::create([
                'campaign_id'        => $campaign->id,
                'recipient_email'    => $recipient['email'],
                'recipient_name'     => $recipient['name'] ?? 'Recipient',
                'recipient_location' => $recipient['location'] ?? null,
                'recipient_type'     => $recipient['type'] ?? 'crm',
                'status'             => 'pending',
                'next_attempt_at'    => now()->startOfDay()->addDay(),
            ]);

            Log::info("All API providers exhausted. Email to {$recipient['email']} queued as pending.");
            return 'pending';
        }

        try {
            $result = $this->sendViaApi($provider, $campaign, $recipient);

            if ($result['success']) {
                $provider->incrementSent();
                $campaign->increment('sent_count');
                Log::info("Email sent via {$provider->name} ({$provider->driver}) to {$recipient['email']}.");
                return 'sent';
            }

            throw new \Exception($result['error'] ?? 'API returned failure response');

        } catch (\Exception $e) {
            Log::error("API [{$provider->name}] failed for {$recipient['email']}: " . $e->getMessage());

            // Try the next available provider before giving up
            $fallback = SmtpProvider::getAvailableExcept($provider->id);

            if ($fallback) {
                try {
                    $result = $this->sendViaApi($fallback, $campaign, $recipient);

                    if ($result['success']) {
                        $fallback->incrementSent();
                        $campaign->increment('sent_count');
                        Log::info("Fallback: Email sent via {$fallback->name} to {$recipient['email']}.");
                        return 'sent';
                    }
                } catch (\Exception $fe) {
                    Log::error("Fallback [{$fallback->name}] also failed: " . $fe->getMessage());
                }
            }

            // All failed — queue as pending
            PendingEmail::create([
                'campaign_id'        => $campaign->id,
                'recipient_email'    => $recipient['email'],
                'recipient_name'     => $recipient['name'] ?? 'Recipient',
                'recipient_location' => $recipient['location'] ?? null,
                'recipient_type'     => $recipient['type'] ?? 'crm',
                'status'             => 'pending',
                'last_error'         => $e->getMessage(),
                'attempts'           => 1,
                'next_attempt_at'    => now()->addHour(),
            ]);

            $campaign->increment('failed_count');
            return 'pending';
        }
    }

    /**
     * Retry all pending emails (called by cron).
     * Respects today's fresh limits after midnight reset.
     */
    public function retryPending(): void
    {
        $pending = PendingEmail::readyToSend()->with('campaign')->get();

        if ($pending->isEmpty()) {
            Log::info('SmtpRotationService: No pending emails to retry.');
            return;
        }

        Log::info("SmtpRotationService: Retrying {$pending->count()} pending emails.");

        foreach ($pending as $pendingEmail) {
            $provider = SmtpProvider::getAvailable();

            if (!$provider) {
                Log::info('SmtpRotationService: All providers still at limit. Stopping retry run.');
                break;
            }

            try {
                $result = $this->sendViaApi($provider, $pendingEmail->campaign, [
                    'name'     => $pendingEmail->recipient_name,
                    'email'    => $pendingEmail->recipient_email,
                    'location' => $pendingEmail->recipient_location,
                    'type'     => $pendingEmail->recipient_type,
                ]);

                if (!$result['success']) {
                    throw new \Exception($result['error'] ?? 'API failure on retry');
                }

                $provider->incrementSent();
                $pendingEmail->campaign->increment('sent_count');
                $pendingEmail->delete();

                Log::info("Retry succeeded via {$provider->name} for {$pendingEmail->recipient_email}.");

            } catch (\Exception $e) {
                $pendingEmail->increment('attempts');
                $pendingEmail->update([
                    'last_error'      => $e->getMessage(),
                    'next_attempt_at' => now()->addHours(2),
                ]);

                Log::error("Retry failed for {$pendingEmail->recipient_email}: " . $e->getMessage());
            }
        }
    }

    /**
     * Retry pending emails for a specific campaign immediately.
     */
    public function retryCampaignPending(Campaign $campaign): int
    {
        $pending = PendingEmail::where('campaign_id', $campaign->id)
            ->where('status', 'pending')
            ->get();

        if ($pending->isEmpty()) {
            return 0;
        }

        $sentCount = 0;
        foreach ($pending as $pendingEmail) {
            $provider = SmtpProvider::getAvailable();

            if (!$provider) {
                Log::info("SmtpRotationService: All providers exhausted during retry for campaign {$campaign->id}.");
                break;
            }

            try {
                $result = $this->sendViaApi($provider, $campaign, [
                    'name'     => $pendingEmail->recipient_name,
                    'email'    => $pendingEmail->recipient_email,
                    'location' => $pendingEmail->recipient_location,
                    'type'     => $pendingEmail->recipient_type,
                ]);

                if (!$result['success']) {
                    throw new \Exception($result['error'] ?? 'API failure on retry');
                }

                $provider->incrementSent();
                
                // Decrement failed count and increment sent count
                $campaign->decrement('failed_count');
                $campaign->increment('sent_count');
                
                $pendingEmail->delete();
                $sentCount++;

                Log::info("Retry succeeded via {$provider->name} for {$pendingEmail->recipient_email}.");

            } catch (\Exception $e) {
                $pendingEmail->increment('attempts');
                $pendingEmail->update([
                    'last_error'      => $e->getMessage(),
                    'next_attempt_at' => now()->addHours(2),
                ]);

                Log::error("Retry failed for {$pendingEmail->recipient_email}: " . $e->getMessage());
            }
        }

        return $sentCount;
    }

    /**
     * Dispatch the email via the correct API based on the driver type.
     *
     * @return array{success: bool, error?: string}
     */
    private function sendViaApi(SmtpProvider $provider, $campaign, array $recipient): array
    {
        $subject     = $campaign->subject;
        $htmlContent = $this->buildHtmlBody($campaign->body, $recipient);
        $fromEmail   = $provider->from_email;
        $fromName    = $provider->from_name;
        $toEmail     = $recipient['email'];
        $toName      = $recipient['name'] ?? 'Recipient';
        $apiKey      = $provider->api_key;

        return match($provider->driver) {
            'brevo'   => $this->sendViaBrevo($apiKey, $fromEmail, $fromName, $toEmail, $toName, $subject, $htmlContent),
            'smtp2go' => $this->sendViaSmtp2go($apiKey, $fromEmail, $fromName, $toEmail, $toName, $subject, $htmlContent),
            default   => ['success' => false, 'error' => "Unknown driver: {$provider->driver}"],
        };
    }

    /**
     * Send via Brevo Transactional Email API.
     * https://api.brevo.com/v3/smtp/email
     */
    private function sendViaBrevo(
        string $apiKey,
        string $fromEmail,
        string $fromName,
        string $toEmail,
        string $toName,
        string $subject,
        string $htmlContent
    ): array {
        $response = Http::withHeaders([
            'accept'       => 'application/json',
            'api-key'      => $apiKey,
            'content-type' => 'application/json',
        ])->post('https://api.brevo.com/v3/smtp/email', [
            'sender'      => ['name' => $fromName, 'email' => $fromEmail],
            'to'          => [['email' => $toEmail, 'name' => $toName]],
            'subject'     => $subject,
            'htmlContent' => $htmlContent,
        ]);

        if ($response->successful()) {
            return ['success' => true];
        }

        $body = $response->json();
        return [
            'success' => false,
            'error'   => "Brevo API error [{$response->status()}]: " . ($body['message'] ?? $response->body()),
        ];
    }

    /**
     * Send via SMTP2GO API.
     * https://api.smtp2go.com/v3/email/send
     */
    private function sendViaSmtp2go(
        string $apiKey,
        string $fromEmail,
        string $fromName,
        string $toEmail,
        string $toName,
        string $subject,
        string $htmlContent
    ): array {
        $response = Http::withHeaders([
            'Content-Type'      => 'application/json',
            'X-Smtp2go-Api-Key' => $apiKey,
            'accept'            => 'application/json',
        ])->post('https://api.smtp2go.com/v3/email/send', [
            'api_key'   => $apiKey,
            'sender'    => "{$fromName} <{$fromEmail}>",
            'to'        => ["{$toName} <{$toEmail}>"],
            'subject'   => $subject,
            'html_body' => $htmlContent,
        ]);

        if ($response->successful()) {
            $body = $response->json();
            // SMTP2GO returns {"data":{"succeeded":1}} on success
            if (isset($body['data']['succeeded']) && $body['data']['succeeded'] > 0) {
                return ['success' => true];
            }
            $error = $body['data']['failures'][0] ?? ($body['data']['error'] ?? 'Unknown SMTP2GO failure');
            return ['success' => false, 'error' => "SMTP2GO: {$error}"];
        }

        $body = $response->json();
        return [
            'success' => false,
            'error'   => "SMTP2GO API error [{$response->status()}]: " . ($body['data']['error'] ?? $response->body()),
        ];
    }

    /**
     * Build the HTML body with recipient-specific variable replacement.
     */
    private function buildHtmlBody(string $body, array $recipient): string
    {
        $replaced = str_replace(
            [
                '{{client_name}}', '{{recipient_name}}', '{{client_email}}', '{{recipient_email}}', '{{client_location}}',
                '{client_name}', '{recipient_name}', '{client_email}', '{recipient_email}', '{client_location}'
            ],
            [
                $recipient['name'] ?? '', $recipient['name'] ?? '', $recipient['email'] ?? '', $recipient['email'] ?? '', $recipient['location'] ?? '',
                $recipient['name'] ?? '', $recipient['name'] ?? '', $recipient['email'] ?? '', $recipient['email'] ?? '', $recipient['location'] ?? ''
            ],
            $body
        );

        // Remove any remaining unresolved placeholders like {{variable}} or {variable}
        $replaced = preg_replace('/\{\{\s*[a-zA-Z0-9_-]+\s*\}\}/', '', $replaced);
        return preg_replace('/\{\s*[a-zA-Z0-9_-]+\s*\}/', '', $replaced);
    }

    /**
     * Send raw email (e.g. system notification or standard Mailer call) via active API provider rotation.
     *
     * @return bool
     */
    public function sendRaw(string $subject, string $htmlContent, string $toEmail, string $toName = 'Recipient'): bool
    {
        $provider = SmtpProvider::getAvailable();

        if (!$provider) {
            Log::warning("RotationTransport: All SMTP providers are exhausted. Cannot send raw email to {$toEmail}.");
            return false;
        }

        try {
            $result = $this->sendViaApiRaw(
                $provider,
                $subject,
                $htmlContent,
                $toEmail,
                $toName
            );

            if ($result['success']) {
                $provider->incrementSent();
                Log::info("Raw email sent successfully via {$provider->name} ({$provider->driver}) to {$toEmail}.");
                return true;
            }

            throw new \Exception($result['error'] ?? 'API returned failure response');

        } catch (\Exception $e) {
            Log::error("Raw email [{$provider->name}] failed for {$toEmail}: " . $e->getMessage());

            // Try the next available provider before giving up
            $fallback = SmtpProvider::getAvailableExcept($provider->id);

            if ($fallback) {
                try {
                    $result = $this->sendViaApiRaw(
                        $fallback,
                        $subject,
                        $htmlContent,
                        $toEmail,
                        $toName
                    );

                    if ($result['success']) {
                        $fallback->incrementSent();
                        Log::info("Fallback raw email sent successfully via {$fallback->name} to {$toEmail}.");
                        return true;
                    }
                } catch (\Exception $fe) {
                    Log::error("Fallback raw email [{$fallback->name}] also failed: " . $fe->getMessage());
                }
            }

            return false;
        }
    }

    private function sendViaApiRaw(
        SmtpProvider $provider,
        string $subject,
        string $htmlContent,
        string $toEmail,
        string $toName
    ): array {
        $fromEmail = $provider->from_email;
        $fromName  = $provider->from_name;
        $apiKey    = $provider->api_key;

        return match($provider->driver) {
            'brevo'   => $this->sendViaBrevo($apiKey, $fromEmail, $fromName, $toEmail, $toName, $subject, $htmlContent),
            'smtp2go' => $this->sendViaSmtp2go($apiKey, $fromEmail, $fromName, $toEmail, $toName, $subject, $htmlContent),
            default   => ['success' => false, 'error' => "Unknown driver: {$provider->driver}"],
        };
    }
}
