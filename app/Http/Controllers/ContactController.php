<?php

namespace App\Http\Controllers;

use App\Models\ContactMessage;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function store(Request $request, NotificationService $notifications)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:160'],
            'phone' => ['nullable', 'string', 'max:40'],
            'company_name' => ['nullable', 'string', 'max:160'],
            'interest' => ['nullable', 'string', 'max:120'],
            'message' => ['required', 'string', 'max:2000'],
        ]);

        $message = ContactMessage::query()->create([
            ...$data,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        $notifications->notifyAdmin(
            'contact.created',
            'New Agape153 inquiry',
            "New inquiry from {$message->name} ({$message->email}): {$message->message}",
            ['contact_message_id' => $message->id]
        );

        return back()->with('status', 'Thank you. Your message has been sent to the Agape153 team.');
    }
}
