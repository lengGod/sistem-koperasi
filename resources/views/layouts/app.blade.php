<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <link rel="icon" href="{{ asset('favicon.ico') }}?v=2" type="image/x-icon">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}?v=2" type="image/x-icon">
    <title>{{ isset($title) ? $title . ' | ' : '' }}Koperasi Siger - Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&amp;display=swap"
        rel="stylesheet" />
    <link
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap"
        rel="stylesheet" />
    @vite(['resources/css/app.scss', 'resources/js/app.js'])
</head>

<body class="bg-surface text-on-surface antialiased">
    @php
        $navItems = [
            [
                'label' => 'Dashboard',
                'icon' => 'dashboard',
                'href' => route('dashboard'),
                'active' => request()->routeIs('dashboard'),
            ],
            [
                'label' => 'Anggota',
                'icon' => 'group',
                'href' => route('members.index'),
                'active' => request()->routeIs('members.*'),
            ],
            [
                'label' => 'Jenis Simpanan',
                'icon' => 'category',
                'href' => route('savings-types.index'),
                'active' => request()->routeIs('savings-types.*'),
            ],
            [
                'label' => 'Simpanan',
                'icon' => 'account_balance',
                'href' => route('savings.index'),
                'active' => request()->routeIs('savings.*'),
            ],
            [
                'label' => 'Pinjaman',
                'icon' => 'payments',
                'href' => route('loans.index'),
                'active' => request()->routeIs('loans.*'),
            ],
            [
                'label' => 'Angsuran',
                'icon' => 'event_repeat',
                'href' => route('installments.index'),
                'active' => request()->routeIs('installments.*'),
            ],
        ];
    @endphp

    <div x-data="appShell" @open-confirm-modal.window="openConfirm($event.detail)"
        class="flex min-h-screen w-full">
        <!-- Sidebar Backdrop for mobile -->
        <div x-cloak x-show="sidebarOpen" @click="sidebarOpen = false"
            x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-40 bg-black/40 lg:hidden"></div>

        <!-- Sidebar (off-canvas on mobile, fixed on desktop so it never scrolls and never overlaps content) -->
        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
            class="fixed inset-y-0 left-0 z-50 flex w-64 shrink-0 flex-col border-r border-outline-variant bg-surface-container-lowest shadow-sm transition-transform duration-300 lg:translate-x-0">
            <div class="flex items-center gap-3 px-6 py-6">
                <img src="{{ asset('logo.png') }}" alt="Logo Koperasi" class="h-12 w-12 shrink-0 object-contain">
                <div class="min-w-0 flex-1">
                    <h1 class="truncate text-lg font-extrabold text-primary">Koperasi Digital</h1>
                    <p class="text-xs font-semibold uppercase tracking-[0.14em] text-outline">Admin Portal</p>
                </div>
                <button @click="sidebarOpen = false"
                    class="rounded-full p-1.5 text-on-surface-variant hover:bg-surface-container lg:hidden transition"
                    aria-label="Close Sidebar">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>

            <nav class="flex-1 overflow-y-auto px-4 py-2">
                <ul class="space-y-1">
                    @foreach ($navItems as $item)
                        <li>
                            <a href="{{ $item['href'] }}"
                                class="{{ $item['active'] ? 'border-primary bg-secondary-container text-on-secondary-container shadow-sm' : 'border-transparent text-on-surface-variant hover:bg-secondary-container hover:text-on-secondary-container' }} flex items-center gap-3 border-l-4 px-4 py-3 text-sm font-semibold transition-all active:scale-[0.99]">
                                <span
                                    class="material-symbols-outlined {{ $item['active'] ? 'icon-fill' : '' }}">{{ $item['icon'] }}</span>
                                <span>{{ $item['label'] }}</span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </nav>

            <div class="border-t border-outline-variant px-4 py-4">
                <div class="mb-3 flex items-center gap-3 px-2">
                    <span
                        class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-primary text-sm font-bold text-on-primary">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </span>
                    <div class="min-w-0 flex-1">
                        <p class="truncate text-sm font-bold text-on-surface">{{ auth()->user()->name }}</p>
                        <p class="truncate text-xs text-outline">{{ auth()->user()->email }}</p>
                    </div>
                </div>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit"
                        class="flex w-full items-center gap-3 rounded-xl border border-outline-variant bg-surface-container-low px-4 py-2.5 text-sm font-bold text-error transition hover:bg-error-container hover:border-error">
                        <span class="material-symbols-outlined text-[20px]">logout</span>
                        Keluar
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Content Column -->
        <div class="app-main">
            <!-- Topbar (sticky, never scrolls) -->
            <header
                class="sticky top-0 z-30 flex h-16 shrink-0 items-center justify-between border-b border-outline-variant bg-surface-container-lowest/95 px-6 backdrop-blur">
                <div class="flex flex-1 items-center gap-4">
                    <button @click="sidebarOpen = !sidebarOpen"
                        class="rounded-full p-2 text-on-surface-variant hover:bg-surface-container lg:hidden transition"
                        aria-label="Toggle Sidebar">
                        <span class="material-symbols-outlined">menu</span>
                    </button>

                    @if (isset($header))
                        <div class="topbar-page-title">
                            {{ $header }}
                        </div>
                    @endif
                </div>

                <div class="flex items-center gap-4">
                    <div class="relative" @click.outside="profileOpen = false">
                        <button @click="profileOpen = !profileOpen" type="button"
                            class="flex items-center gap-2 rounded-full py-1 pl-1 pr-2 transition hover:bg-surface-container">
                            <span
                                class="flex h-9 w-9 items-center justify-center rounded-full bg-primary text-sm font-bold text-on-primary">
                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                            </span>
                            <span class="material-symbols-outlined text-on-surface-variant">expand_more</span>
                        </button>

                        <div x-cloak x-show="profileOpen" x-transition
                            class="absolute right-0 mt-2 w-56 overflow-hidden rounded-xl border border-outline-variant bg-surface-container-lowest shadow-lg">
                            <div class="border-b border-outline-variant px-4 py-3">
                                <p class="truncate text-sm font-bold">{{ auth()->user()->name }}</p>
                            </div>
                            <a href="{{ route('profile.edit') }}"
                                class="flex items-center gap-2 px-4 py-3 text-sm font-semibold text-on-surface transition hover:bg-surface-container-low">
                                <span class="material-symbols-outlined text-[20px]">person</span>
                                Profil
                            </a>
                            <form action="{{ route('logout') }}" method="POST" class="border-t border-outline-variant"
                                style="margin-top: 0.75rem;">
                                @csrf
                                <button type="submit"
                                    class="flex w-full items-center gap-2 px-4 py-3 text-left text-sm font-semibold text-error transition hover:bg-error-container">
                                    <span class="material-symbols-outlined text-[20px]">logout</span>
                                    Keluar
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </header>

            <main class="flex-1">
                <div class="mx-auto max-w-[1440px] px-4 py-6 md:px-6 lg:px-8">
                    {{ $slot }}
                </div>
            </main>
        </div>

        <!-- Confirmation Modal -->
        <div x-cloak x-show="confirmOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div x-show="confirmOpen" x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0" @click="closeConfirm()" class="fixed inset-0 bg-black/40"></div>

            <div x-show="confirmOpen" x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                class="relative z-10 w-full max-w-md overflow-hidden rounded-3xl border border-outline-variant bg-surface-container-lowest p-6 shadow-2xl">
                <div class="flex items-start gap-4">
                    <div :class="{
                        'bg-error-container text-on-error-container': confirmTone === 'danger',
                        'bg-primary-container text-on-primary': confirmTone !== 'danger'
                    }"
                        class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl">
                        <span class="material-symbols-outlined"
                            x-text="confirmTone === 'danger' ? 'warning' : 'info'"></span>
                    </div>
                    <div class="min-w-0 flex-1">
                        <h3 class="text-xl font-bold text-on-surface" x-text="confirmTitle"></h3>
                        <p class="mt-2 text-sm text-outline" x-text="confirmMessage"></p>
                    </div>
                </div>

                <div class="mt-6 flex justify-end gap-2">
                    <button type="button" @click="closeConfirm()"
                        class="rounded-xl border border-outline-variant bg-surface-container-lowest px-4 py-2 text-sm font-bold text-on-surface-variant transition hover:bg-surface-container-low">
                        Batal
                    </button>
                    <button type="button" @click="submitConfirm()"
                        :class="{
                            'bg-error text-white hover:bg-opacity-90': confirmTone === 'danger',
                            'bg-primary text-on-primary hover:bg-opacity-90': confirmTone !== 'danger'
                        }"
                        class="rounded-xl px-4 py-2 text-sm font-bold shadow-sm transition" x-text="confirmButton">
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Toast Notification -->
    @php
        $toastMessage = session('error') ?? session('status');
        $toastTone = session('error') ? 'error' : 'success';
    @endphp
    @if ($toastMessage)
        <div id="app-toast" data-tone="{{ $toastTone }}" class="fixed bottom-6 right-6 z-[60] w-full max-w-sm">
            <div
                class="flex items-start gap-3 rounded-2xl border p-4 shadow-xl
            {{ $toastTone === 'error'
                ? 'border-red-300 bg-red-50 text-red-900'
                : 'border-emerald-400 bg-emerald-50 text-emerald-900' }}">
                <div
                    class="flex h-8 w-8 shrink-0 items-center justify-center rounded-xl
                {{ $toastTone === 'error' ? 'bg-red-500 text-white' : 'bg-emerald-500 text-white' }}">
                    <span class="material-symbols-outlined icon-fill text-[18px]">
                        {{ $toastTone === 'error' ? 'error' : 'check_circle' }}
                    </span>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-bold">{{ $toastTone === 'error' ? 'Gagal' : 'Berhasil' }}</p>
                    <p class="mt-0.5 text-sm">{{ $toastMessage }}</p>
                </div>
                <button id="app-toast-close" class="shrink-0 rounded-lg p-1 opacity-60 transition hover:opacity-100">
                    <span class="material-symbols-outlined text-[18px]">close</span>
                </button>
            </div>
        </div>
    @endif
</body>

</html>
