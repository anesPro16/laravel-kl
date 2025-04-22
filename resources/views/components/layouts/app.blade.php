<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ isset($title) ? $title.' - '.config('app.name') : config('app.name') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        th{color: #010100}
        .dark th{color: var(--fallback-bc, oklch(var(--bc) / 1));}
    </style>
</head>
<body class="min-h-screen font-sans antialiased bg-base-200/50 dark:bg-base-200">

    {{-- NAVBAR mobile only --}}
    <x-nav sticky class="lg:hidden">
        <x-slot:brand>
            <x-app-brand />
        </x-slot:brand>
        <x-slot:actions>
            <label for="main-drawer" class="lg:hidden me-3">
                <x-icon name="o-bars-3" class="cursor-pointer" />
            </label>
        </x-slot:actions>
    </x-nav>

    {{-- MAIN --}}
    <x-main full-width>
        {{-- SIDEBAR --}}
        <x-slot:sidebar drawer="main-drawer" collapsible class="bg-base-100 lg:bg-inherit">

            {{-- BRAND --}}
            <x-app-brand class="p-5 pt-3" />

            {{-- MENU --}}
            <x-menu activate-by-route>

                {{-- User --}}
                @if($user = auth()->user())
                    <x-menu-separator />

                    <x-list-item :item="$user" value="name" sub-value="email" no-separator no-hover class="-mx-2 !-my-2 rounded">
                        <x-slot:actions>
                            <x-button icon="o-power" class="btn-circle btn-ghost btn-xs" tooltip-left="logoff" wire-navigate link="/logout" />
                            {{-- <x-button icon="o-power" class="btn-circle btn-ghost btn-xs" tooltip-left="logoff" no-wire-navigate link="/logout" /> --}}
                        </x-slot:actions>
                    </x-list-item>

                    <x-menu-separator />
                @endif
                <x-menu-sub title="Penjualan" icon="s-building-storefront">
                    <x-menu-item title="Kasir" icon="s-shopping-cart" link="/cashier" />
                    <x-menu-item title="Daftar Penjualan" icon="c-presentation-chart-line" link="#" />
                </x-menu-sub>

                <x-menu-sub title="Pembelian" icon="m-gift">
                    <x-menu-item title="Pesanan Pembelian" icon="m-gift-top" link="#" />
                    <x-menu-item title="Daftar Pembelian" icon="c-presentation-chart-bar" link="#" />
                </x-menu-sub>

                <x-menu-sub title="Master Data" icon="s-circle-stack">
                    <x-menu-item title="Data Produk" icon="m-archive-box" link="/product" />
                    <x-menu-item title="Data Kategori" icon="m-sparkles" link="/category" />
                    <x-menu-item title="Data Satuan" icon="m-cube" link="/unit" />
                    <x-menu-item title="Data Rak" icon="c-folder-open" link="####" />
                </x-menu-sub>
                @guest
                    <x-menu-item title="Login" icon="o-arrow-left-end-on-rectangle" link="/login" />
                @endguest
                <x-theme-toggle class="btn btn-circle btn-ghost" />
            </x-menu>
        </x-slot:sidebar>

        {{-- The `$slot` goes here --}}
        <x-slot:content>
            {{ $slot }}
        </x-slot:content>
    </x-main>

    {{--  TOAST area --}}
    <x-toast />
</body>
</html>
