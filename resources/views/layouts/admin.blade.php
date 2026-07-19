<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin - Agape153')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="admin-shell bg-[#f8faf9] font-sans text-slate-900"
    data-admin-timeout-minutes="{{ (int) config('session.admin_lifetime', 30) }}"
    data-admin-logout-url="{{ route('admin.logout') }}"
    data-admin-login-url="{{ route('admin.login') }}">
    <div data-admin-overlay class="fixed inset-0 z-40 bg-slate-950/45 opacity-0 backdrop-blur-sm transition pointer-events-none lg:hidden"></div>

    <div class="min-h-screen lg:grid lg:grid-cols-[280px_minmax(0,1fr)]">
        <header class="sticky top-0 z-30 border-b border-slate-200 bg-white/90 px-4 py-3 shadow-sm backdrop-blur-xl lg:hidden">
            <div class="flex items-center justify-between gap-3">
                <a class="inline-flex min-w-0 items-center gap-3 font-black text-teal-800" href="{{ route('admin.dashboard') }}" aria-label="Agape153 admin dashboard">
                    <x-logo variant="compact" />
                </a>
                <div class="flex items-center gap-2">
                    <span class="hidden max-w-[38vw] truncate text-sm font-black text-slate-800 sm:inline">{{ auth()->user()->name }}</span>
                    <button class="icon-button" type="button" data-admin-menu-toggle aria-label="Open admin menu" aria-expanded="false">
                        <x-icon name="menu" class="h-5 w-5" />
                    </button>
                </div>
            </div>
        </header>

        <aside data-admin-sidebar class="fixed inset-y-0 left-0 z-50 w-[min(21rem,calc(100vw-1.5rem))] -translate-x-full overflow-y-auto border-r border-slate-200 bg-white p-4 shadow-[24px_0_70px_rgba(15,23,42,0.22)] transition-transform duration-300 lg:sticky lg:top-0 lg:h-screen lg:w-auto lg:translate-x-0 lg:shadow-none lg:p-5 lg:pb-10">
            <div class="flex items-center justify-between gap-3">
                <a class="inline-flex items-center gap-3 font-black text-teal-800 transition hover:-translate-y-0.5" href="{{ route('admin.dashboard') }}">
                    <x-logo variant="compact" />
                    <span class="sr-only">Agape153 Admin</span>
                </a>
                <button class="icon-button lg:hidden" type="button" data-admin-menu-close aria-label="Close admin menu">
                    <x-icon name="x" class="h-5 w-5" />
                </button>
            </div>
            <nav class="mt-5 grid gap-2 pb-8 text-sm font-bold lg:mt-8">
                @php($adminUser = auth()->user())
                <a class="nav-link rounded-xl px-3 py-2 hover:bg-teal-50" href="{{ route('admin.dashboard') }}"><x-icon name="dashboard" class="h-4 w-4" />Dashboard</a>
                @if ($adminUser->hasPermission('manage-orders'))
                    <a class="nav-link rounded-xl px-3 py-2 hover:bg-teal-50" href="{{ route('admin.orders.index') }}"><x-icon name="orders" class="h-4 w-4" />Orders</a>
                    <a class="nav-link rounded-xl px-3 py-2 hover:bg-teal-50" href="{{ route('admin.customers.index') }}"><x-icon name="user" class="h-4 w-4" />Customers</a>
                @endif
                @if ($adminUser->hasPermission('view-reports'))
                    <a class="nav-link rounded-xl px-3 py-2 hover:bg-teal-50" href="{{ route('admin.reports.index') }}"><x-icon name="chart" class="h-4 w-4" />Reports</a>
                @endif
                @if ($adminUser->hasPermission('view-analytics'))
                    <a class="nav-link rounded-xl px-3 py-2 hover:bg-teal-50" href="{{ route('admin.analytics.index') }}"><x-icon name="chart" class="h-4 w-4" />Analytics</a>
                @endif
                @if ($adminUser->hasPermission('manage-products'))
                    <a class="nav-link rounded-xl px-3 py-2 hover:bg-teal-50" href="{{ route('admin.products.index') }}"><x-icon name="package" class="h-4 w-4" />Products</a>
                    <a class="nav-link rounded-xl px-3 py-2 hover:bg-teal-50" href="{{ route('admin.stock-movements.index') }}"><x-icon name="history" class="h-4 w-4" />Stock Logs</a>
                @endif
                @if ($adminUser->hasPermission('manage-categories'))
                    <a class="nav-link rounded-xl px-3 py-2 hover:bg-teal-50" href="{{ route('admin.categories.index') }}"><x-icon name="package" class="h-4 w-4" />Categories</a>
                @endif
                @if ($adminUser->hasPermission('manage-messages'))
                    <a class="nav-link rounded-xl px-3 py-2 hover:bg-teal-50" href="{{ route('admin.contact-messages.index') }}"><x-icon name="message" class="h-4 w-4" />Messages</a>
                    <a class="nav-link rounded-xl px-3 py-2 hover:bg-teal-50" href="{{ route('admin.notification-logs.index') }}"><x-icon name="message" class="h-4 w-4" />Notifications</a>
                @endif
                @if ($adminUser->hasPermission('manage-cms'))
                    <a class="nav-link rounded-xl px-3 py-2 hover:bg-teal-50" href="{{ route('admin.cms.index') }}"><x-icon name="settings" class="h-4 w-4" />CMS</a>
                    <a class="nav-link rounded-xl px-3 py-2 hover:bg-teal-50" href="{{ route('admin.appearance.edit') }}"><x-icon name="settings" class="h-4 w-4" />Appearance</a>
                    <a class="nav-link rounded-xl px-3 py-2 hover:bg-teal-50" href="{{ route('admin.translations.index') }}"><x-icon name="language" class="h-4 w-4" />Translations</a>
                @endif
                @if ($adminUser->hasPermission('manage-users'))
                    <a class="nav-link rounded-xl px-3 py-2 hover:bg-teal-50" href="{{ route('admin.staff.index') }}"><x-icon name="user" class="h-4 w-4" />Staff</a>
                @endif
                <a class="nav-link rounded-xl px-3 py-2 hover:bg-teal-50" href="{{ route('home') }}"><x-icon name="home" class="h-4 w-4" />View Website</a>
            </nav>
        </aside>
        <main class="min-w-0 p-4 sm:p-5 lg:p-8">
            <div class="mb-6 flex flex-col justify-between gap-4 rounded-2xl border border-slate-200 bg-white px-4 py-3 shadow-sm md:flex-row md:items-center">
                <div class="flex items-center gap-3">
                    <span class="grid h-10 w-10 place-items-center rounded-xl bg-teal-50 text-teal-700">
                        <x-icon name="shield" class="h-5 w-5" />
                    </span>
                    <div>
                        <div class="text-sm font-black text-slate-950">{{ auth()->user()->name }}</div>
                        <div class="flex items-center gap-1 text-xs font-semibold text-slate-500">
                            <x-icon name="history" class="h-3.5 w-3.5" />
                            Session timeout after {{ (int) config('session.admin_lifetime', 30) }} minutes idle
                        </div>
                    </div>
                </div>
                <form class="md:self-center" method="POST" action="{{ route('admin.logout') }}">
                    @csrf
                    <button class="btn-secondary w-full md:w-auto" type="submit" title="Logout admin" aria-label="Logout admin">
                        <x-icon name="logout" class="h-4 w-4" />
                        Logout
                    </button>
                </form>
            </div>

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
