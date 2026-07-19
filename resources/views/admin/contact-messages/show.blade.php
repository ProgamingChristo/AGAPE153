@extends('layouts.admin')

@section('title', 'Contact Message - Admin Agape153')

@section('content')
    <div class="flex flex-col justify-between gap-4 md:flex-row md:items-end">
        <div>
            <p class="text-sm font-black uppercase tracking-[0.22em] text-teal-700">Contact Message</p>
            <h1 class="mt-3 text-4xl font-black text-slate-950">{{ $message->name }}</h1>
        </div>
        <a class="btn-secondary" href="{{ route('admin.contact-messages.index') }}">Back</a>
    </div>

    <div class="mt-8 grid gap-6 lg:grid-cols-[1fr_340px]">
        <article class="rounded-2xl border border-slate-200 bg-white p-6">
            <div class="mb-5 flex flex-wrap gap-2">
                <span class="rounded-full bg-teal-100 px-3 py-1 text-sm font-black text-teal-800">{{ strtoupper($message->status) }}</span>
                <span class="rounded-full bg-slate-100 px-3 py-1 text-sm font-bold text-slate-700">{{ $message->interest ?: 'General inquiry' }}</span>
            </div>
            <p class="whitespace-pre-line leading-8 text-slate-700">{{ $message->message }}</p>

            @if ($message->reply_message)
                <div class="mt-6 rounded-2xl border border-teal-200 bg-teal-50 p-5">
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <div>
                            <div class="text-xs font-black uppercase tracking-[0.16em] text-teal-700">Admin Reply</div>
                            <h2 class="mt-1 text-xl font-black text-slate-950">{{ $message->reply_subject }}</h2>
                        </div>
                        <div class="text-xs font-bold text-slate-500">
                            {{ $message->replied_at?->format('d M Y H:i') }}
                        </div>
                    </div>
                    <p class="mt-4 whitespace-pre-line text-sm leading-7 text-teal-950">{{ $message->reply_message }}</p>
                </div>
            @endif

            <form class="mt-6 grid gap-4 rounded-2xl border border-slate-200 bg-[#f8faf9] p-5" method="POST" action="{{ route('admin.contact-messages.reply', $message) }}">
                @csrf
                <div>
                    <p class="text-sm font-black uppercase tracking-[0.18em] text-teal-700">Reply Customer</p>
                    <h2 class="mt-2 text-2xl font-black text-slate-950">Kirim balasan langsung.</h2>
                    <p class="mt-2 text-sm leading-6 text-slate-600">Balasan akan dikirim ke email customer, dicatat di notification log, dan jika ada nomor telepon juga dibuat notifikasi WhatsApp.</p>
                </div>
                <label class="grid gap-2 text-sm font-bold text-slate-700">Subject
                    <input class="rounded-xl border border-slate-200 bg-white px-4 py-3 font-normal" name="reply_subject" value="{{ old('reply_subject', $message->reply_subject ?: 'Reply from Agape153') }}" required>
                </label>
                <label class="grid gap-2 text-sm font-bold text-slate-700">Message
                    <textarea class="min-h-44 rounded-xl border border-slate-200 bg-white px-4 py-3 font-normal" name="reply_message" placeholder="Tulis balasan untuk customer..." required>{{ old('reply_message', $message->reply_message) }}</textarea>
                </label>
                <button class="btn-primary w-max" type="submit">
                    <x-icon name="mail" class="h-4 w-4" />
                    Send Reply
                </button>
            </form>
        </article>

        <aside class="h-max rounded-2xl border border-slate-200 bg-[#f8faf9] p-6">
            <h2 class="text-xl font-black text-slate-950">Contact detail</h2>
            <dl class="mt-5 grid gap-4 text-sm">
                <div>
                    <dt class="text-slate-500">Email</dt>
                    <dd class="font-black text-slate-950"><a href="mailto:{{ $message->email }}">{{ $message->email }}</a></dd>
                </div>
                <div>
                    <dt class="text-slate-500">Phone</dt>
                    <dd class="font-black text-slate-950">{{ $message->phone ?: '-' }}</dd>
                </div>
                <div>
                    <dt class="text-slate-500">Company</dt>
                    <dd class="font-black text-slate-950">{{ $message->company_name ?: '-' }}</dd>
                </div>
                <div>
                    <dt class="text-slate-500">Received</dt>
                    <dd class="font-black text-slate-950">{{ $message->created_at->format('d M Y H:i') }}</dd>
                </div>
            </dl>

            <form class="mt-6 grid gap-3" method="POST" action="{{ route('admin.contact-messages.update', $message) }}">
                @csrf
                @method('PUT')
                <select class="rounded-xl border border-slate-200 px-4 py-3 text-sm" name="status">
                    @foreach (['new', 'read', 'replied', 'archived'] as $status)
                        <option value="{{ $status }}" @selected($message->status === $status)>{{ ucfirst($status) }}</option>
                    @endforeach
                </select>
                <button class="btn-primary" type="submit">Update Status</button>
            </form>

            <form class="mt-3" method="POST" action="{{ route('admin.contact-messages.destroy', $message) }}" onsubmit="return confirm('Delete this message?')">
                @csrf
                @method('DELETE')
                <button class="btn-secondary w-full" type="submit">Delete Message</button>
            </form>
        </aside>
    </div>
@endsection
