<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class ContactMessageController extends Controller
{
    public function index()
    {
        return view('admin.contact-messages.index', [
            'messages' => ContactMessage::query()->latest()->paginate(15),
            'newCount' => ContactMessage::query()->where('status', 'new')->count(),
        ]);
    }

    public function show(ContactMessage $contactMessage)
    {
        $contactMessage->markAsRead();

        return view('admin.contact-messages.show', [
            'message' => $contactMessage->fresh(),
        ]);
    }

    public function update(Request $request, ContactMessage $contactMessage)
    {
        $data = $request->validate([
            'status' => ['required', 'in:new,read,replied,archived'],
        ]);

        $contactMessage->update($data);

        return back()->with('status', 'Message status updated.');
    }

    public function reply(Request $request, ContactMessage $contactMessage, NotificationService $notifications)
    {
        $data = $request->validate([
            'reply_subject' => ['required', 'string', 'max:180'],
            'reply_message' => ['required', 'string', 'max:4000'],
        ]);

        $contactMessage->update([
            'reply_subject' => $data['reply_subject'],
            'reply_message' => $data['reply_message'],
            'replied_by' => $request->user()->id,
            'replied_at' => now(),
            'status' => 'replied',
        ]);

        $notifications->sendEmail(
            'contact.replied',
            $contactMessage->email,
            $data['reply_subject'],
            $data['reply_message'],
            ['contact_message_id' => $contactMessage->id]
        );

        if ($contactMessage->phone) {
            $notifications->sendWhatsApp(
                'contact.replied',
                $contactMessage->phone,
                "Agape153 reply: {$data['reply_message']}",
                ['contact_message_id' => $contactMessage->id]
            );
        }

        return back()->with('status', 'Reply berhasil dikirim dan disimpan.');
    }

    public function destroy(ContactMessage $contactMessage)
    {
        $contactMessage->delete();

        return redirect()->route('admin.contact-messages.index')->with('status', 'Message deleted.');
    }
}
