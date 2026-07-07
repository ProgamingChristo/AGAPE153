<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Agape153 - Indonesian Spices & Coffee Export')</title>
    <meta name="description" content="@yield('description', 'Agape153 memasok rempah-rempah dan kopi Indonesia untuk pasar lokal dan ekspor global.')">
    <link rel="canonical" href="{{ url()->current() }}">
    <meta property="og:title" content="@yield('title', 'Agape153')">
    <meta property="og:description" content="@yield('description', 'Indonesian spices and coffee supplier.')">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta name="twitter:card" content="summary_large_image">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @yield('schema')
</head>
<body class="min-h-screen font-sans antialiased">
    @php
        $cartCount = collect(session('cart', []))->sum('quantity');
    @endphp

    <header class="sticky top-0 z-40 border-b border-black/5 bg-white/90 backdrop-blur">
        <nav class="agape-container flex min-h-16 items-center justify-between gap-6">
            <a href="{{ route('home') }}" class="flex items-center gap-3 font-black tracking-tight text-teal-800">
                <span class="grid h-9 w-9 place-items-center rounded-xl bg-teal-700 text-white">153</span>
                <span>Agape153</span>
            </a>
            <div class="hidden items-center gap-6 text-sm font-semibold text-slate-700 md:flex">
                <a class="hover:text-teal-700" href="{{ route('home') }}#about">About</a>
                <a class="hover:text-teal-700" href="{{ route('products.index') }}">Products</a>
                <a class="hover:text-teal-700" href="{{ route('home') }}#export">Export</a>
                <a class="hover:text-teal-700" href="{{ route('orders.track') }}">Tracking</a>
                <a class="hover:text-teal-700" href="{{ route('home') }}#contact">Contact</a>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('cart.index') }}" class="rounded-xl border border-slate-200 px-3 py-2 text-sm font-bold text-slate-700 hover:border-teal-300 hover:text-teal-700">
                    Cart {{ $cartCount ? "({$cartCount})" : '' }}
                </a>
                @auth
                    <a href="{{ auth()->user()->isAdmin() ? route('admin.dashboard') : route('member.dashboard') }}" class="hidden rounded-xl bg-slate-900 px-3 py-2 text-sm font-bold text-white sm:inline-flex">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="hidden rounded-xl bg-slate-900 px-3 py-2 text-sm font-bold text-white sm:inline-flex">Login</a>
                @endauth
            </div>
        </nav>
    </header>

    @if (session('status'))
        <div class="agape-container mt-5 rounded-xl border border-teal-200 bg-teal-50 px-4 py-3 text-sm font-semibold text-teal-800">
            {{ session('status') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="agape-container mt-5 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
            <strong>Periksa kembali input:</strong>
            <ul class="mt-1 list-inside list-disc">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <main>
        @yield('content')
    </main>

    <footer class="border-t border-slate-200 bg-white">
        <div class="agape-container grid gap-8 py-10 md:grid-cols-4">
            <div class="md:col-span-2">
                <div class="text-xl font-black text-teal-800">Agape153</div>
                <p class="mt-3 max-w-xl text-sm leading-6 text-slate-600">Supplier rempah-rempah dan kopi Indonesia untuk pembeli lokal, distributor, horeca, dan importir global.</p>
            </div>
            <div>
                <div class="font-bold text-slate-900">Company</div>
                <div class="mt-3 grid gap-2 text-sm text-slate-600">
                    <a href="{{ route('home') }}#about">Company Profile</a>
                    <a href="{{ route('home') }}#certifications">Certifications</a>
                    <a href="{{ route('home') }}#faq">FAQ</a>
                </div>
            </div>
            <div>
                <div class="font-bold text-slate-900">Commerce</div>
                <div class="mt-3 grid gap-2 text-sm text-slate-600">
                    <a href="{{ route('products.index') }}">Product Catalog</a>
                    <a href="{{ route('cart.index') }}">Cart</a>
                    <a href="{{ route('orders.track') }}">Order Tracking</a>
                </div>
            </div>
        </div>
    </footer>
</body>
</html>
