@extends('layouts.admin')

@section('title', 'Translations - Admin Agape153')

@section('content')
    <div>
        <p class="text-sm font-black uppercase tracking-[0.22em] text-teal-700">Translations</p>
        <h1 class="mt-3 text-4xl font-black text-slate-950">Multilingual content.</h1>
    </div>

    <form class="mt-8 grid gap-3 rounded-2xl border border-slate-200 bg-white p-6 md:grid-cols-[120px_160px_1fr]" method="POST" action="{{ route('admin.translations.store') }}">
        @csrf
        <select class="rounded-xl border border-slate-200 px-4 py-3" name="locale" required>
            <option value="id">ID</option>
            <option value="en">EN</option>
        </select>
        <input class="rounded-xl border border-slate-200 px-4 py-3" name="group" value="site" placeholder="Group" required>
        <input class="rounded-xl border border-slate-200 px-4 py-3" name="key" placeholder="Key, e.g. nav.about" required>
        <textarea class="min-h-24 rounded-xl border border-slate-200 px-4 py-3 md:col-span-3" name="value" placeholder="Translation value"></textarea>
        <button class="btn-primary md:col-span-3 md:justify-self-start" type="submit">Save Translation</button>
    </form>

    <div class="mt-8 overflow-hidden rounded-2xl border border-slate-200 bg-white">
        @forelse ($translations as $translation)
            <form class="grid gap-3 border-b border-slate-100 p-4 md:grid-cols-[80px_140px_220px_1fr_auto]" method="POST" action="{{ route('admin.translations.store') }}">
                @csrf
                <input class="rounded-xl border border-slate-200 px-3 py-2 font-bold uppercase" name="locale" value="{{ $translation->locale }}" readonly>
                <input class="rounded-xl border border-slate-200 px-3 py-2" name="group" value="{{ $translation->group }}" readonly>
                <input class="rounded-xl border border-slate-200 px-3 py-2" name="key" value="{{ $translation->key }}" readonly>
                <input class="rounded-xl border border-slate-200 px-3 py-2" name="value" value="{{ $translation->value }}">
                <button class="btn-secondary px-3 py-2 text-sm" type="submit">Save</button>
            </form>
        @empty
            <div class="p-6 text-slate-600">No translations yet.</div>
        @endforelse
    </div>
@endsection
