@extends('layouts.admin')

@section('title', 'Notification Logs - Admin Agape153')

@section('content')
    <div>
        <p class="text-sm font-black uppercase tracking-[0.22em] text-teal-700">Notifications</p>
        <h1 class="mt-3 text-4xl font-black text-slate-950">Email and WhatsApp logs.</h1>
    </div>

    <div class="mt-8 overflow-hidden rounded-2xl border border-slate-200 bg-white">
        @forelse ($logs as $log)
            <div class="grid gap-4 border-b border-slate-100 p-5 md:grid-cols-[120px_160px_1fr_110px] md:items-center">
                <div class="font-black uppercase text-slate-700">{{ $log->channel }}</div>
                <div>
                    <div class="font-bold text-slate-950">{{ $log->event }}</div>
                    <div class="text-xs text-slate-500">{{ $log->created_at->format('d M Y H:i') }}</div>
                </div>
                <div>
                    <div class="text-sm font-bold text-slate-700">{{ $log->recipient ?: '-' }}</div>
                    <div class="mt-1 line-clamp-2 text-sm text-slate-500">{{ $log->message }}</div>
                    @if ($log->error_message)
                        <div class="mt-1 text-xs font-bold text-red-700">{{ $log->error_message }}</div>
                    @endif
                </div>
                <div class="rounded-full bg-slate-100 px-3 py-1 text-center text-xs font-black uppercase text-slate-700">{{ $log->status }}</div>
            </div>
        @empty
            <div class="p-6 text-slate-600">No notification logs yet.</div>
        @endforelse
    </div>
    <div class="mt-6">{{ $logs->links() }}</div>
@endsection
