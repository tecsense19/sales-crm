<?php

namespace App\Mail\Transport;

use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mailer\Transport\AbstractTransport;
use Symfony\Component\Mime\MessageConverter;
use App\Services\SmtpRotationService;
use Illuminate\Support\Facades\Log;

class RotationTransport extends AbstractTransport
{
    /**
     * Send the message using the SmtpRotationService.
     */
    protected function doSend(SentMessage $message): void
    {
        try {
            $email = MessageConverter::toEmail($message->getOriginalMessage());
            
            $subject = $email->getSubject() ?: '';
            $htmlContent = $email->getHtmlBody() ?: '';
            
            if (empty($htmlContent)) {
                $htmlContent = nl2br(e($email->getTextBody() ?: ''));
            }
            
            $to = $email->getTo();
            if (empty($to)) {
                Log::warning('RotationTransport: Email has no recipients.');
                return;
            }
            
            $recipientEmail = $to[0]->getAddress();
            $recipientName = $to[0]->getName() ?: 'Recipient';
            
            $rotationService = app(SmtpRotationService::class);
            $rotationService->sendRaw($subject, $htmlContent, $recipientEmail, $recipientName);
        } catch (\Exception $e) {
            Log::error('RotationTransport error: ' . $e->getMessage());
        }
    }

    /**
     * Get the string representation of the transport.
     */
    public function __toString(): string
    {
        return 'rotation';
    }
}
