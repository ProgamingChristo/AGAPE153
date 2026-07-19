@extends('layouts.admin')

@section('title', 'Contact Messages - Admin Agape153')

@section('content')
    <div class="flex flex-col justify-between gap-4 md:flex-row md:items-end">
        <div>
            <p class="text-sm font-black uppercase tracking-[0.22em] text-teal-700">Contact Messages</p>
            <h1 class="mt-3 text-4xl font-black text-slate-950">Buyer inquiries.</h1>
            <p class="mt-3 text-sm font-semibold text-slate-500">{{ $newCount }} new message{{ $newCount === 1 ? '' : 's' }} waiting.</p>
        </div>
    </div>

    <div class="mt-8 overflow-hidden rounded-2xl border border-slate-200 bg-white">
        @forelse ($messages as $message)
            <a class="grid gap-4 border-b border-slate-100 p-5 transition hover:bg-teal-50 md:grid-cols-[1fr_180px_120px_120px] md:items-center" href="{{ route('admin.contact-messages.show', $message) }}">
                <div>
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="font-black text-slate-950">{{ $message->name }}</span>
                        @if ($message->status === 'new')
                            <span class="rounded-full bg-teal-100 px-2 py-1 text-xs font-black text-teal-800">NEW</span>
                        @endif
                    </div>
                    <div class="mt-1 text-sm text-slate-500">{{ $message->email }} {{ $message->company_name ? '/ '.$message->company_name : '' }}</div>
                </div>
                <div class="text-sm font-bold text-slate-700">{{ $message->interest ?: 'General inquiry' }}</div>
                <div class="text-sm font-black uppercase text-slate-600">{{ $message->status }}</div>
                <div class="text-sm text-slate-500">{{ $message->created_at->format('d M Y') }}</div>
            </a>
        @empty
            <div class="p-8 text-slate-600">No contact messages yet.</div>
        @endforelse
    </div>

    <div class="mt-6">{{ $messages->links() }}</div>
@endsection
