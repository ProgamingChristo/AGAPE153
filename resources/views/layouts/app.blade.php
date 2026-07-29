<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', $siteText['meta.title'] ?? 'Agape153 - Indonesian Spices & Coffee Supplier')</title>
    <meta name="description" content="@yield('description', $siteText['meta.description'] ?? 'Agape153 supplies Indonesian spices, coffee, and agricultural commodities.')">
    <link rel="canonical" href="{{ url()->current() }}">
    <meta property="og:title" content="@yield('title', 'Agape153')">
    <meta property="og:description" content="@yield('description', 'Indonesian spices and coffee supplier.')">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta name="twitter:card" content="summary_large_image">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="{{ $siteAppearance['font_url'] ?? 'https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap' }}" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @yield('schema')
</head>
<body class="user-shell min-h-screen font-sans antialiased" style="--agape-primary: {{ $siteAppearance['primary_color'] ?? '#0f766e' }}; --agape-accent: {{ $siteAppearance['accent_color'] ?? '#e9c95a' }}; --agape-soft: {{ $siteAppearance['soft_color'] ?? '#edf7f4' }}; --agape-font-family: {{ $siteAppearance['font_stack'] ?? "'Plus Jakarta Sans', ui-sans-serif, system-ui, sans-serif" }}; font-family: {{ $siteAppearance['font_stack'] ?? "'Plus Jakarta Sans', ui-sans-serif, system-ui, sans-serif" }};">
    @php
        $cartCount = collect(session('cart', []))->sum('quantity');
        $t = $siteText ?? [];
    @endphp

    <div class="cursor-glow" data-cursor-glow></div>
    <div class="cursor-ring" data-cursor-ring></div>
    <div class="cursor-dot" data-cursor-dot></div>

    <header data-site-header class="sticky top-0 z-40 border-b border-white/70 bg-white/75 shadow-[0_18px_48px_rgba(15,23,42,0.08)] backdrop-blur-2xl">
        <div data-site-topbar class="border-b border-slate-200/70 bg-white/55">
            <div class="agape-container flex min-h-9 items-center justify-between gap-3 overflow-x-auto text-xs font-bold text-slate-600">
                <div class="flex shrink-0 items-center gap-2">
                    <span class="h-2 w-2 rounded-full bg-teal-600 shadow-[0_0_0_4px_rgba(13,148,136,0.12)]"></span>
                    {{ $t['topbar.tagline'] ?? 'Indonesian agriculture, spices, and coffee for international buyers' }}
                </div>
                <div class="flex shrink-0 items-center gap-4">
                    <a class="inline-flex items-center gap-1 hover:text-teal-700" href="mailto:{{ $siteContact['email'] }}">
                        <x-icon name="mail" class="h-3.5 w-3.5" />
                        {{ $siteContact['email'] }}
                    </a>
                    <a class="inline-flex items-center gap-1 hover:text-teal-700" href="https://wa.me/{{ preg_replace('/\D+/', '', $siteContact['whatsapp']) }}" target="_blank" rel="noopener">
                        <x-icon name="phone" class="h-3.5 w-3.5" />
                        {{ $siteContact['phone_display'] ?? $siteContact['phone'] }}
                    </a>
                </div>
            </div>
        </div>
        <nav class="agape-container flex flex-wrap items-center justify-between gap-3 py-3 lg:flex-nowrap">
            <a href="{{ route('home') }}" class="shrink-0 transition hover:-translate-y-0.5" aria-label="Agape153 home">
                <x-logo variant="compact" />
            </a>
            <div data-site-nav-menu class="order-3 flex w-full items-center gap-1 overflow-x-auto rounded-full border border-slate-200/80 bg-white/80 px-2 py-1 text-sm font-semibold text-slate-700 shadow-sm md:order-none md:w-auto md:overflow-visible">
                <a class="nav-link shrink-0" href="{{ route('about') }}"><x-icon name="globe" class="h-4 w-4" />{{ $t['nav.about'] ?? 'About' }}</a>
                <a class="nav-link shrink-0" href="{{ route('products.index') }}"><x-icon name="package" class="h-4 w-4" />{{ $t['nav.products'] ?? 'Products' }}</a>
                <a class="nav-link shrink-0" href="{{ route('orders.track') }}"><x-icon name="truck" class="h-4 w-4" />{{ $t['nav.shipping'] ?? 'Shipping' }}</a>
                @auth
                    @unless (auth()->user()->isAdmin())
                        <a class="nav-link shrink-0" href="{{ route('member.purchase-history') }}"><x-icon name="orders" class="h-4 w-4" />{{ $t['nav.invoice'] ?? 'Invoice' }}</a>
                    @endunless
                @endauth
                <a class="nav-link shrink-0" href="{{ route('home') }}#contact"><x-icon name="phone" class="h-4 w-4" />{{ $t['nav.contact'] ?? 'Contact Us' }}</a>
            </div>
            <div class="flex items-center gap-2">
                <form method="POST" action="{{ route('language.switch', app()->getLocale() === 'en' ? 'id' : 'en') }}">
                    @csrf
                    <button class="icon-button" type="submit" title="{{ $t['nav.language_title'] ?? 'Switch language' }}" aria-label="{{ $t['nav.language_title'] ?? 'Switch language' }}">
                        <span class="text-xs font-black uppercase">{{ app()->getLocale() === 'en' ? 'ID' : 'EN' }}</span>
                    </button>
                </form>
                <a href="{{ route('cart.index') }}" class="icon-button relative" title="{{ $t['nav.cart'] ?? 'Cart' }}" aria-label="{{ $t['nav.cart'] ?? 'Cart' }}">
                    <x-icon name="cart" class="h-5 w-5" />
                    @if ($cartCount)
                        <span class="absolute -right-1 -top-1 grid h-5 min-w-5 place-items-center rounded-full bg-teal-700 px-1 text-xs font-black text-white">{{ $cartCount }}</span>
                    @endif
                </a>
                <a href="https://wa.me/{{ preg_replace('/\D+/', '', $siteContact['whatsapp']) }}" target="_blank" rel="noopener" class="hidden shrink-0 whitespace-nowrap rounded-full bg-amber-300 px-4 py-2 text-sm font-black leading-none text-slate-950 shadow-[0_12px_26px_rgba(234,179,8,0.22)] transition hover:-translate-y-0.5 hover:bg-amber-200 lg:inline-flex lg:items-center lg:gap-2">
                    <x-icon name="phone" class="h-4 w-4" />
                    WhatsApp
                </a>
                @auth
                    <a href="{{ auth()->user()->isAdmin() ? route('admin.dashboard') : route('member.dashboard') }}" class="icon-button sm:hidden" title="Dashboard" aria-label="Open dashboard">
                        <x-icon name="dashboard" class="h-5 w-5" />
                    </a>
                    <a href="{{ auth()->user()->isAdmin() ? route('admin.dashboard') : route('member.dashboard') }}" class="hidden shrink-0 whitespace-nowrap rounded-full bg-[#101820] px-4 py-2 text-sm font-bold leading-none text-white shadow-[0_12px_26px_rgba(16,24,32,0.18)] transition hover:-translate-y-0.5 hover:bg-teal-800 sm:inline-flex sm:items-center sm:gap-2">
                        <x-icon name="dashboard" class="h-4 w-4" />
                        {{ $t['nav.dashboard'] ?? 'Dashboard' }}
                    </a>
                    <form class="shrink-0" method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="btn-secondary whitespace-nowrap px-3 py-2 text-sm leading-none" type="submit" title="{{ $t['nav.logout'] ?? 'Logout' }}" aria-label="{{ $t['nav.logout'] ?? 'Logout' }}">
                            <x-icon name="logout" class="h-4 w-4" />
                            <span class="hidden sm:inline">{{ $t['nav.logout'] ?? 'Logout' }}</span>
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="icon-button sm:hidden" title="Login" aria-label="Login">
                        <x-icon name="login" class="h-5 w-5" />
                    </a>
                    <a href="{{ route('login') }}" class="hidden shrink-0 whitespace-nowrap rounded-full bg-[#101820] px-4 py-2 text-sm font-bold leading-none text-white shadow-[0_12px_26px_rgba(16,24,32,0.18)] transition hover:-translate-y-0.5 hover:bg-teal-800 sm:inline-flex sm:items-center sm:gap-2">
                        <x-icon name="login" class="h-4 w-4" />
                        {{ $t['nav.login'] ?? 'Login' }}
                    </a>
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

    <footer class="border-t border-slate-200 bg-[#101820] text-white">
        <div class="logo-stripe"><span></span><span></span><span></span></div>
        <div class="agape-container grid gap-8 py-10 {{ auth()->check() ? 'xl:grid-cols-[1.15fr_0.85fr]' : '' }}">
            <div class="grid gap-8 md:grid-cols-[1.2fr_0.8fr_0.8fr]">
                <div>
                <x-logo />
                <p class="mt-4 max-w-xl text-sm leading-6 text-slate-300">{{ $t['footer.description'] ?? $siteContact['footer_description'] }}</p>
                <div class="mt-5 grid max-w-xl gap-3 sm:grid-cols-3">
                    <div class="border border-white/10 bg-white/10 p-4">
                        <div class="text-lg font-black text-amber-200">ID</div>
                        <div class="text-xs font-bold text-slate-300">Sourcing origin</div>
                    </div>
                    <div class="border border-white/10 bg-white/10 p-4">
                        <div class="text-lg font-black text-amber-200">B2B</div>
                        <div class="text-xs font-bold text-slate-300">Buyer workflow</div>
                    </div>
                    <div class="border border-white/10 bg-white/10 p-4">
                        <div class="text-lg font-black text-amber-200">153</div>
                        <div class="text-xs font-bold text-slate-300">Agape trading</div>
                    </div>
                </div>
                <div class="mt-5 flex flex-wrap gap-2">
                    <a class="icon-button" href="{{ $siteContact['youtube_url'] }}" target="_blank" rel="noopener" title="YouTube" aria-label="YouTube"><x-icon name="youtube" class="h-5 w-5" /></a>
                    <a class="icon-button" href="{{ $siteContact['instagram_url'] }}" target="_blank" rel="noopener" title="Instagram" aria-label="Instagram"><x-icon name="instagram" class="h-5 w-5" /></a>
                    <a class="icon-button" href="{{ $siteContact['facebook_url'] }}" target="_blank" rel="noopener" title="Facebook" aria-label="Facebook"><x-icon name="facebook" class="h-5 w-5" /></a>
                    <a class="icon-button" href="{{ $siteContact['linkedin_url'] }}" target="_blank" rel="noopener" title="LinkedIn" aria-label="LinkedIn"><x-icon name="linkedin" class="h-5 w-5" /></a>
                    <a class="icon-button" href="{{ $siteContact['tiktok_url'] }}" target="_blank" rel="noopener" title="TikTok" aria-label="TikTok"><x-icon name="tiktok" class="h-5 w-5" /></a>
                    <a class="icon-button" href="{{ $siteContact['threads_url'] }}" target="_blank" rel="noopener" title="Threads" aria-label="Threads"><x-icon name="threads" class="h-5 w-5" /></a>
                </div>
                </div>
                <div>
                <div class="font-bold text-white">{{ $t['footer.company'] ?? 'Company' }}</div>
                <div class="mt-3 grid gap-2 text-sm text-slate-300">
                    <a href="{{ route('about') }}">{{ $t['footer.profile'] ?? 'Company Profile' }}</a>
                    <a href="{{ route('home') }}#certifications">{{ $t['footer.certifications'] ?? 'Certifications' }}</a>
                    <a href="{{ route('home') }}#faq">{{ $t['footer.faq'] ?? 'FAQ' }}</a>
                </div>
                </div>
                <div>
                <div class="font-bold text-white">{{ $t['footer.buyer_tools'] ?? 'Buyer Tools' }}</div>
                <div class="mt-3 grid gap-2 text-sm text-slate-300">
                    <a href="{{ route('products.index') }}">{{ $t['footer.catalog'] ?? 'Catalog' }}</a>
                    <a href="{{ route('cart.index') }}">{{ $t['nav.cart'] ?? 'Cart' }}</a>
                    <a href="{{ route('orders.track') }}">{{ $t['footer.shipping'] ?? 'Shipping' }}</a>
                    @auth
                        @unless (auth()->user()->isAdmin())
                            <a href="{{ route('member.purchase-history') }}">{{ $t['nav.invoice'] ?? 'Invoice' }}</a>
                        @endunless
                    @endauth
                </div>
                <div class="mt-8 font-bold text-white">{{ $t['footer.contact'] ?? 'Contact Us' }}</div>
                <div class="mt-3 grid gap-3 text-sm text-slate-300">
                    <a class="inline-flex items-center gap-2 hover:text-amber-200" href="mailto:{{ $siteContact['email'] }}">
                        <x-icon name="mail" class="h-4 w-4 shrink-0 text-amber-200" />
                        <span>{{ $siteContact['email'] }}</span>
                    </a>
                    @if (! empty($siteContact['secondary_email']) && $siteContact['secondary_email'] !== $siteContact['email'])
                        <a class="inline-flex items-center gap-2 hover:text-sky-200" href="mailto:{{ $siteContact['secondary_email'] }}">
                            <x-icon name="mail" class="h-4 w-4 shrink-0 text-sky-200" />
                            <span>{{ $siteContact['secondary_email'] }}</span>
                        </a>
                    @endif
                    <a class="inline-flex items-center gap-2 hover:text-teal-200" href="https://wa.me/{{ preg_replace('/\D+/', '', $siteContact['whatsapp']) }}" target="_blank" rel="noopener">
                        <x-icon name="phone" class="h-4 w-4 shrink-0 text-teal-200" />
                        <span>{{ $siteContact['phone_display'] ?? $siteContact['phone'] }}</span>
                    </a>
                    <a class="btn-primary mt-2 w-full px-3 py-2 text-xs sm:w-max" href="{{ route('home') }}#contact">
                        <x-icon name="message" class="h-4 w-4" />
                        {{ $t['footer.contact_form'] ?? 'Contact Form' }}
                    </a>
                </div>
                </div>
            </div>

            @auth
            <aside class="overflow-hidden rounded-3xl border border-white/20 bg-white/12 p-4 shadow-[0_28px_80px_rgba(0,0,0,0.22)] backdrop-blur-xl">
                <div class="logo-stripe -mx-4 -mt-4 mb-4"><span></span><span></span><span></span></div>
                <div class="flex items-center justify-between border-b border-white/15 pb-4">
                    <div>
                        <div class="text-xs font-black uppercase tracking-[0.18em] text-slate-300">{{ $t['footer.board_kicker'] ?? 'Live sourcing board' }}</div>
                        <div class="mt-1 text-xl font-black text-white">{{ $t['footer.board_title'] ?? 'Buyer-ready flow' }}</div>
                    </div>
                    <x-logo variant="compact" />
                </div>
                <div class="mt-4 grid gap-3">
                    @foreach ([
                        ['label' => $t['footer.catalog_lines'] ?? 'Catalog Lines', 'value' => $siteFooterStats['catalog_lines'] ?? 0, 'value_class' => 'text-3xl text-amber-200'],
                        ['label' => $t['footer.featured_skus'] ?? 'Featured Products', 'value' => $siteFooterStats['featured_skus'] ?? 0, 'value_class' => 'text-3xl text-sky-200'],
                        ['label' => $t['footer.payment'] ?? 'Payment', 'value' => 'Online / WhatsApp', 'value_class' => 'text-xl text-teal-200 text-right'],
                    ] as $metric)
                        <div class="flex items-center justify-between gap-4 rounded-2xl border border-white/10 bg-[#101820]/45 p-4">
                            <span class="text-sm font-bold text-slate-300">{{ $metric['label'] }}</span>
                            <strong class="{{ $metric['value_class'] }} font-black">{{ $metric['value'] }}</strong>
                        </div>
                    @endforeach
                </div>
                <div class="trade-ticker agape-chip-strip mt-4" style="max-width:100%;overflow-x:auto;overflow-y:hidden;scroll-snap-type:x proximity;-webkit-overflow-scrolling:touch;padding-bottom:.35rem;">
                    <span style="flex:0 0 auto;scroll-snap-align:start;background:#e64b3c;color:#fff;">Spices</span>
                    <span style="flex:0 0 auto;scroll-snap-align:start;background:#e9c95a;color:#101820;">Coffee</span>
                    <span style="flex:0 0 auto;scroll-snap-align:start;background:#2d9db7;color:#fff;">Agriculture</span>
                    <span style="flex:0 0 auto;scroll-snap-align:start;background:#e64b3c;color:#fff;">{{ $t['nav.shipping'] ?? 'Shipping' }}</span>
                </div>
            </aside>
            @endauth
        </div>
        <div class="agape-container border-t border-white/10 py-5 text-xs font-semibold text-slate-400">
            &copy; {{ now()->year }} Agape153. {{ $t['footer.copyright'] ?? 'Indonesian commodity trading desk.' }}
        </div>
    </footer>
    <x-flash-alerts />
</body>
</html>
