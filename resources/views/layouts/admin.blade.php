<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin - Agape153')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#f8faf9] font-sans text-slate-900">
    <div class="min-h-screen lg:grid lg:grid-cols-[260px_1fr]">
        <aside class="border-r border-slate-200 bg-white p-5">
            <a class="flex items-center gap-3 font-black text-teal-800" href="{{ route('admin.dashboard') }}">
                <span class="grid h-9 w-9 place-items-center rounded-xl bg-teal-700 text-white">153</span>
                <span>Agape153 Admin</span>
            </a>
            <nav class="mt-8 grid gap-2 text-sm font-bold">
                <a class="rounded-xl px-3 py-2 hover:bg-teal-50 hover:text-teal-800" href="{{ route('admin.dashboard') }}">Dashboard</a>
                <a class="rounded-xl px-3 py-2 hover:bg-teal-50 hover:text-teal-800" href="{{ route('admin.products.index') }}">Products</a>
                <a class="rounded-xl px-3 py-2 hover:bg-teal-50 hover:text-teal-800" href="{{ route('admin.categories.index') }}">Categories</a>
                <a class="rounded-xl px-3 py-2 hover:bg-teal-50 hover:text-teal-800" href="{{ route('home') }}">View Website</a>
            </nav>
            <form class="mt-8" method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="btn-secondary w-full" type="submit">Logout</button>
            </form>
        </aside>
        <main class="p-5 lg:p-8">
            @if (session('status'))
                <div class="mb-6 rounded-xl border border-teal-200 bg-teal-50 px-4 py-3 text-sm font-semibold text-teal-800">{{ session('status') }}</div>
            @endif

            @if ($errors->any())
                <div class="mb-6 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                    <strong>Periksa kembali input:</strong>
                    <ul class="mt-1 list-inside list-disc">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </main>
    </div>
</body>
</html>
