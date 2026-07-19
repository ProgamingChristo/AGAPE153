<?php

namespace App\Services;

use App\Models\NotificationLog;
use App\Models\WebsiteSetting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;

class NotificationService
{
    public function sendEmail(string $event, ?string $recipient, string $subject, string $message, array $payload = []): void
    {
        $log = NotificationLog::query()->create([
            'channel' => 'email',
            'event' => $event,
            'recipient' => $recipient,
            'subject' => $subject,
            'message' => $message,
            'payload' => $payload,
            'status' => 'pending',
        ]);

        if (! $recipient) {
            $log->update(['status' => 'skipped', 'error_message' => 'Recipient empty.']);

            return;
        }

        try {
            Mail::raw($message, fn ($mail) => $mail->to($recipient)->subject($subject));
            $log->update(['status' => 'sent']);
        } catch (\Throwable $exception) {
            $log->update(['status' => 'failed', 'error_message' => $exception->getMessage()]);
        }
    }

    public function sendWhatsApp(string $event, ?string $recipient, string $message, array $payload = []): void
    {
        $recipient = $recipient ? preg_replace('/\D+/', '', $recipient) : null;
        $log = NotificationLog::query()->create([
            'channel' => 'whatsapp',
            'event' => $event,
            'recipient' => $recipient,
            'message' => $message,
            'payload' => $payload,
            'status' => 'pending',
        ]);

        if (! $recipient) {
            $log->update(['status' => 'skipped', 'error_message' => 'Recipient empty.']);

            return;
        }

        $webhookUrl = config('services.whatsapp.webhook_url');

        if (! $webhookUrl) {
            $log->update(['status' => 'logged', 'payload' => [...$payload, 'wa_url' => "https://wa.me/{$recipient}?text=".rawurlencode($message)]]);

            return;
        }

        try {
            $response = Http::withToken((string) config('services.whatsapp.webhook_token'))
                ->post($webhookUrl, [
                    'to' => $recipient,
                    'message' => $message,
                    'event' => $event,
                    'payload' => $payload,
                ]);

            $log->update([
                'status' => $response->successful() ? 'sent' : 'failed',
                'error_message' => $response->successful() ? null : $response->body(),
            ]);
        } catch (\Throwable $exception) {
            $log->update(['status' => 'failed', 'error_message' => $exception->getMessage()]);
        }
    }

    public function notifyAdmin(string $event, string $subject, string $message, array $payload = []): void
    {
        $email = WebsiteSetting::value('company_email', 'info.agape153@gmail.com');
        $phone = WebsiteSetting::value('whatsapp_number', '+62816795153');

        $this->sendEmail($event, $email, $subject, $message, $payload);
        $this->sendWhatsApp($event, $phone, $message, $payload);
    }
}
