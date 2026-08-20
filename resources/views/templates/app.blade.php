<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>{{ $title ?? 'Stokin - Aplikasi Stok & Transaksi' }}</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
    </head>
    <body class="bg-gray-100">
        <div class="flex h-screen overflow-hidden">
            <!-- Sidebar -->
            <aside class="w-64 bg-white border-r border-gray-200 flex-shrink-0 hidden md:block">
                <div class="p-5 border-b border-gray-200">
                    <h1 class="text-xl font-bold text-gray-800">Stokin</h1>
                    <p class="text-xs text-gray-500 mt-1">Aplikasi Stok & Transaksi</p>
                </div>
                <nav class="p-4 space-y-1">
                    <a wire:navigate href="{{ route('dashboard') }}" @class([
                        'flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition',
                        'bg-blue-50 text-blue-700' => request()->routeIs('dashboard'),
                        'text-gray-700 hover:bg-gray-100' => !request()->routeIs('dashboard'),
                    ])>
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                        Dashboard
                    </a>
                    <a wire:navigate href="{{ route('customers.index') }}" @class([
                        'flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition',
                        'bg-blue-50 text-blue-700' => request()->routeIs('customers.*'),
                        'text-gray-700 hover:bg-gray-100' => !request()->routeIs('customers.*'),
                    ])>
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        Pelanggan
                    </a>
                    <a wire:navigate href="{{ route('transactions.index') }}" @class([
                        'flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition',
                        'bg-blue-50 text-blue-700' => request()->routeIs('transactions.*'),
                        'text-gray-700 hover:bg-gray-100' => !request()->routeIs('transactions.*'),
                    ])>
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                        Transaksi
                    </a>
                    <a wire:navigate href="{{ route('debt-payments.index') }}" @class([
                        'flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition',
                        'bg-blue-50 text-blue-700' => request()->routeIs('debt-payments.*'),
                        'text-gray-700 hover:bg-gray-100' => !request()->routeIs('debt-payments.*'),
                    ])>
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Pembayaran Hutang
                    </a>
                    <a wire:navigate href="{{ route('expenses.index') }}" @class([
                        'flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition',
                        'bg-blue-50 text-blue-700' => request()->routeIs('expenses.*'),
                        'text-gray-700 hover:bg-gray-100' => !request()->routeIs('expenses.*'),
                    ])>
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"/></svg>
                        Pengeluaran
                    </a>
                </nav>
            </aside>

            <!-- Main Content -->
            <main class="flex-1 overflow-y-auto">
                <!-- Mobile Header -->
                <div class="md:hidden bg-white border-b border-gray-200 px-4 py-3 flex items-center justify-between">
                    <h1 class="text-lg font-bold text-gray-800">Stokin</h1>
                </div>

                <div class="p-4 md:p-6 lg:p-8">
                    {{ $slot }}
                </div>
            </main>
        </div>

        @livewireScripts
    </body>
</html>
