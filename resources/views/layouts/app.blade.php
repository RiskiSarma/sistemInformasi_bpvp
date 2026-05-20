<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" id="html-root">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>BPVP Banda Aceh - Sistem Informasi</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link rel="icon" type="image/png" href="{{ asset('images/logo blk banda.png') }}">

    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <!-- Tailwind CDN + Dark Mode Config -->
    <script>
        // ── Cegah flash: terapkan tema SEBELUM render ──
        (function () {
            const saved = localStorage.getItem('theme');
            const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            if (saved === 'dark' || (!saved && prefersDark)) {
                document.getElementById('html-root').classList.add('dark');
            }
        })();
    </script>
    <script src="https://cdn.tailwindcss.com"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script>
        tailwind.config = { darkMode: 'class' }
    </script>

    <style>
/* ══════════════════════════════════════════
   DARK MODE AUTO-OVERRIDE — child pages
   Tidak perlu ubah kode di tiap halaman
   ══════════════════════════════════════════ */

/* ── Background ── */
html.dark .bg-white          { background-color: #1e293b !important; }
html.dark .bg-gray-50        { background-color: #0f172a !important; }
html.dark .bg-gray-100       { background-color: #1e293b !important; }
html.dark .bg-gray-200       { background-color: #334155 !important; }

/* ── Text ── */
html.dark .text-gray-900     { color: #f1f5f9 !important; }
html.dark .text-gray-800     { color: #e2e8f0 !important; }
html.dark .text-gray-700     { color: #cbd5e1 !important; }
html.dark .text-gray-600     { color: #94a3b8 !important; }
html.dark .text-gray-500     { color: #64748b !important; }
html.dark .text-gray-400     { color: #475569 !important; }

/* ── Border ── */
html.dark .border-gray-100   { border-color: #1e293b !important; }
html.dark .border-gray-200   { border-color: #334155 !important; }
html.dark .border-gray-300   { border-color: #475569 !important; }
html.dark .divide-gray-200   > * { border-color: #334155 !important; }

/* ── Hover states ── */
html.dark .hover\:bg-gray-50:hover  { background-color: #1e293b !important; }
html.dark .hover\:bg-gray-100:hover { background-color: #334155 !important; }

/* ── Table ── */
html.dark table               { color: #cbd5e1; }
html.dark thead               { background-color: #1e293b !important; }
html.dark thead th            { color: #94a3b8 !important; background-color: #1e293b !important; }
html.dark tbody tr            { border-color: #334155 !important; }
html.dark tbody tr:hover      { background-color: #1e293b !important; }
html.dark .divide-y > tr      { border-color: #334155 !important; }

/* ── Form Input / Select / Textarea ── */
html.dark input:not([type="checkbox"]):not([type="radio"]):not([type="range"]),
html.dark textarea,
html.dark select {
    background-color: #1e293b !important;
    border-color:     #475569 !important;
    color:            #e2e8f0 !important;
}
html.dark input::placeholder,
html.dark textarea::placeholder { color: #475569 !important; }
html.dark input:focus,
html.dark textarea:focus,
html.dark select:focus {
    border-color: #0d9488 !important;
    box-shadow: 0 0 0 3px rgba(13,148,136,.2) !important;
    outline: none;
}
html.dark input[disabled],
html.dark textarea[disabled],
html.dark select[disabled] {
    background-color: #0f172a !important;
    opacity: .6;
}

/* ── Card / Panel ── */
html.dark .shadow-sm   { box-shadow: 0 1px 3px rgba(0,0,0,.4) !important; }
html.dark .shadow-md   { box-shadow: 0 4px 12px rgba(0,0,0,.4) !important; }
html.dark .rounded-lg,
html.dark .rounded-xl  { }  /* radius tidak perlu diubah */

/* ── Alert / Notification ── */
html.dark .bg-green-50  { background-color: rgba(16,185,129,.12) !important; }
html.dark .bg-red-50    { background-color: rgba(239,68,68,.12)  !important; }
html.dark .bg-blue-50   { background-color: rgba(59,130,246,.12) !important; }
html.dark .bg-yellow-50 { background-color: rgba(245,158,11,.12) !important; }
html.dark .bg-indigo-50 { background-color: rgba(99,102,241,.12) !important; }
html.dark .bg-purple-50 { background-color: rgba(168,85,247,.12) !important; }

html.dark .border-green-200  { border-color: rgba(16,185,129,.3)  !important; }
html.dark .border-red-200    { border-color: rgba(239,68,68,.3)   !important; }
html.dark .border-blue-200   { border-color: rgba(59,130,246,.3)  !important; }
html.dark .border-yellow-200 { border-color: rgba(245,158,11,.3)  !important; }

html.dark .text-green-800 { color: #6ee7b7 !important; }
html.dark .text-red-800   { color: #fca5a5 !important; }
html.dark .text-blue-800  { color: #93c5fd !important; }

/* ── Badge / Pill ── */
html.dark .bg-emerald-100 { background-color: rgba(16,185,129,.2)  !important; }
html.dark .bg-blue-100    { background-color: rgba(59,130,246,.2)  !important; }
html.dark .bg-red-100     { background-color: rgba(239,68,68,.2)   !important; }
html.dark .bg-yellow-100  { background-color: rgba(245,158,11,.2)  !important; }
html.dark .bg-purple-100  { background-color: rgba(168,85,247,.2)  !important; }
html.dark .bg-indigo-100  { background-color: rgba(99,102,241,.2)  !important; }
html.dark .bg-slate-100   { background-color: rgba(100,116,139,.2) !important; }

html.dark .text-emerald-700 { color: #6ee7b7 !important; }
html.dark .text-blue-700    { color: #93c5fd !important; }
html.dark .text-red-700     { color: #fca5a5 !important; }
html.dark .text-yellow-700  { color: #fde68a !important; }
html.dark .text-purple-700  { color: #d8b4fe !important; }
html.dark .text-indigo-700  { color: #a5b4fc !important; }
html.dark .text-slate-700   { color: #94a3b8  !important; }

/* ── Modal / Dialog ── */
html.dark .fixed.inset-0 > .bg-white,
html.dark [role="dialog"] .bg-white { background-color: #1e293b !important; }

/* ── Scrollbar ── */
html.dark ::-webkit-scrollbar       { width: 6px; height: 6px; }
html.dark ::-webkit-scrollbar-track { background: #0f172a; }
html.dark ::-webkit-scrollbar-thumb { background: #475569; border-radius: 4px; }
html.dark ::-webkit-scrollbar-thumb:hover { background: #64748b; }

/* ── Transisi halus ── */
*, *::before, *::after {
    transition-property: background-color, border-color, color, box-shadow;
    transition-duration: 180ms;
    transition-timing-function: ease;
}
</style>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Flowbite -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.js"></script>

    <!-- jQuery -->
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>

    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.1/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.14.1/dist/cdn.min.js"></script>
</head>
@stack('scripts')
<body class="font-sans antialiased bg-gray-50 dark:bg-gray-950 text-gray-900 dark:text-gray-100"
      x-data="{ sidebarOpen: false }">

    <div class="min-h-screen flex">

        {{-- ══════ SIDEBAR ══════ --}}
        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
               class="fixed inset-y-0 left-0 z-50 w-64
                      bg-white dark:bg-gray-900
                      border-r border-transparent dark:border-gray-800
                      shadow-xl dark:shadow-none
                      transform transition-transform duration-300 ease-in-out
                      lg:translate-x-0 flex flex-col">

            {{-- Logo --}}
            <div class="flex items-center justify-between h-16 px-6
                        border-b border-gray-100 dark:border-gray-800 flex-shrink-0">
                <div class="flex items-center space-x-3">
                    <img src="{{ asset('images/logo-bpvp.png') }}" alt="Logo BPVP"
                         class="h-10 w-auto"
                         onerror="this.src='{{ asset('images/logo-bpvp.png') }}'">
                    <span class="text-sm font-bold text-gray-800 dark:text-gray-100">BPVP Banda Aceh</span>
                </div>
                <button @click="sidebarOpen = false"
                        class="lg:hidden text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            {{-- Navigation --}}
            <nav class="flex-1 px-4 py-6 space-y-1 overflow-y-auto"
                 x-data="{
                    programOpen: {{ request()->routeIs('admin.programs.*','admin.independent-units.*','admin.pendidikan.*','admin.jenis-materi-pelatihan.*','admin.programs.jenis-pelatihan.*','admin.programs.paket-pelatihan.*','admin.programs.kejuruan-bidang.*') ? 'true' : 'false' }},
                    pengajarOpen: {{ request()->routeIs('admin.instructors.*','admin.pengajar-eksternal.*') ? 'true' : 'false' }}
                 }">

                @php
                    $navBase    = 'flex items-center space-x-3 px-4 py-2.5 rounded-xl transition-all duration-150 text-sm font-medium';
                    $navActive  = 'bg-teal-600 text-white shadow-sm';
                    $navInactive= 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800';
                    $subBase    = 'block px-4 py-2 text-sm rounded-lg transition-all duration-150';
                    $subActive  = 'bg-teal-600 text-white shadow-sm';
                    $subInactive= 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800';
                @endphp

                {{-- Dashboard --}}
                <a href="{{ route('dashboard') }}"
                   class="{{ $navBase }} {{ request()->routeIs('dashboard','admin.dashboard') ? $navActive : $navInactive }}">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    <span>Dashboard</span>
                </a>

                {{-- Master Data Dropdown --}}
                <div>
                    <button @click="programOpen = !programOpen"
                            class="{{ $navBase }} w-full {{ $navInactive }} justify-between">
                        <div class="flex items-center space-x-3">
                            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                            <span>Master Data</span>
                        </div>
                        <svg class="w-3.5 h-3.5 transition-transform duration-200" :class="programOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div x-show="programOpen" x-collapse class="mt-1 space-y-0.5 pl-3">
                        <a href="{{ route('admin.programs.index') }}"            class="{{ $subBase }} {{ request()->routeIs('admin.programs.index','admin.programs.create','admin.programs.edit','admin.programs.show') ? $subActive : $subInactive }}">Kelola Pelatihan</a>
                        <a href="{{ route('admin.programs.master') }}"           class="{{ $subBase }} {{ request()->routeIs('admin.programs.master*') ? $subActive : $subInactive }}">Program Pelatihan (Master)</a>
                        <a href="{{ route('admin.independent-units.index') }}"   class="{{ $subBase }} {{ request()->routeIs('admin.independent-units.*') ? $subActive : $subInactive }}">SKKNI & Unit Kompetensi</a>
                        <a href="{{ route('admin.programs.paket-pelatihan.index') }}" class="{{ $subBase }} {{ request()->routeIs('admin.programs.paket-pelatihan.*') ? $subActive : $subInactive }}">Paket Pelatihan</a>
                        <a href="{{ route('admin.jenis-materi-pelatihan.index') }}"   class="{{ $subBase }} {{ request()->routeIs('admin.jenis-materi-pelatihan.*') ? $subActive : $subInactive }}">Jenis Materi Pelatihan</a>
                        <a href="{{ route('admin.pendidikan.index') }}"          class="{{ $subBase }} {{ request()->routeIs('admin.pendidikan.index') ? $subActive : $subInactive }}">Pendidikan</a>
                        <a href="{{ route('admin.programs.jenis-pelatihan.index') }}" class="{{ $subBase }} {{ request()->routeIs('admin.programs.jenis-pelatihan.*') ? $subActive : $subInactive }}">Jenis Pelatihan</a>
                        <a href="{{ route('admin.programs.kejuruan-bidang.index') }}" class="{{ $subBase }} {{ request()->routeIs('admin.programs.kejuruan-bidang.*') ? $subActive : $subInactive }}">Kejuruan Bidang</a>
                    </div>
                </div>

                {{-- Peserta --}}
                <a href="{{ route('admin.participants.index') }}"
                   class="{{ $navBase }} {{ request()->routeIs('admin.participants.*') ? $navActive : $navInactive }}">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                    <span>Peserta</span>
                </a>

                {{-- Daftar Ulang --}}
                <a href="{{ route('admin.daftar-ulang.index') }}"
                   class="{{ $navBase }} {{ request()->routeIs('admin.daftar-ulang.*') ? $navActive : $navInactive }}">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                    </svg>
                    <span>Daftar Ulang</span>
                    @php $pendingCount = \App\Models\ParticipantDocument::where('status','pending')->count(); @endphp
                    @if($pendingCount > 0)
                        <span class="ml-auto text-xs font-bold px-1.5 py-0.5 rounded-full {{ request()->routeIs('admin.daftar-ulang.*') ? 'bg-white text-teal-600' : 'bg-red-500 text-white' }}">{{ $pendingCount }}</span>
                    @endif
                </a>

                {{-- Pengajar Dropdown --}}
                <div>
                    <button @click="pengajarOpen = !pengajarOpen"
                            class="{{ $navBase }} w-full {{ $navInactive }} justify-between">
                        <div class="flex items-center space-x-3">
                            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            <span>Pengajar</span>
                        </div>
                        <svg class="w-3.5 h-3.5 transition-transform duration-200" :class="pengajarOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div x-show="pengajarOpen" x-collapse class="mt-1 space-y-0.5 pl-3">
                        <a href="{{ route('admin.instructors.index') }}"        class="{{ $subBase }} {{ request()->routeIs('admin.instructors.*') ? $subActive : $subInactive }}">Instruktur (Internal)</a>
                        <a href="{{ route('admin.pengajar-eksternal.index') }}" class="{{ $subBase }} {{ request()->routeIs('admin.pengajar-eksternal.*') ? $subActive : $subInactive }}">Pengajar Eksternal</a>
                    </div>
                </div>

                {{-- Single links --}}
                @foreach([
                    ['route' => 'admin.attendance.index',  'pattern' => 'admin.attendance.*',  'icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z', 'label' => 'Kehadiran'],
                    ['route' => 'admin.reports.index',     'pattern' => 'admin.reports.*',     'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z', 'label' => 'Laporan'],
                    ['route' => 'admin.certificates.index','pattern' => 'admin.certificates.*','icon' => 'M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z', 'label' => 'Sertifikat'],
                    ['route' => 'admin.users.index',       'pattern' => 'admin.users.*',       'icon' => 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z', 'label' => 'Manajemen User'],
                ] as $item)
                <a href="{{ route($item['route']) }}"
                   class="{{ $navBase }} {{ request()->routeIs($item['pattern']) ? $navActive : $navInactive }}">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $item['icon'] }}"/>
                    </svg>
                    <span>{{ $item['label'] }}</span>
                </a>
                @endforeach

            </nav>

            {{-- User Info Bottom --}}
            <div class="flex-shrink-0 p-4 border-t border-gray-100 dark:border-gray-800 bg-white dark:bg-gray-900">
                <div class="flex items-center space-x-3 px-3 py-2">
                    <div class="w-9 h-9 bg-teal-600 rounded-full flex items-center justify-center flex-shrink-0">
                        <span class="text-white font-semibold text-sm">{{ substr(Auth::user()->name, 0, 1) }}</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-gray-800 dark:text-gray-100 truncate">{{ Auth::user()->name }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 truncate">{{ Auth::user()->email }}</p>
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}" class="mt-1">
                    @csrf
                    <button type="submit"
                            class="w-full px-3 py-2 text-sm text-red-500 dark:text-red-400
                                   hover:bg-red-50 dark:hover:bg-red-900/30
                                   rounded-lg transition flex items-center justify-center space-x-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                        <span>Logout</span>
                    </button>
                </form>
            </div>
        </aside>

        {{-- Mobile overlay --}}
        <div x-show="sidebarOpen" @click="sidebarOpen = false"
             x-transition:enter="transition-opacity ease-linear duration-300"
             x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-linear duration-300"
             x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-40 bg-black/50 lg:hidden" style="display:none;"></div>

        {{-- ══════ MAIN CONTENT ══════ --}}
        <div class="flex-1 flex flex-col lg:ml-64">

            {{-- Header --}}
            <header class="bg-white dark:bg-gray-900
                           border-b border-gray-200 dark:border-gray-800
                           shadow-sm fixed top-0 right-0 left-0 lg:left-64 z-30">
                <div class="flex items-center justify-between h-16 px-6">
                    <button @click="sidebarOpen = true"
                            class="lg:hidden text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>

                    <div class="flex-1 lg:flex-none">
                        <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100">@yield('title', 'Dashboard')</h2>
                    </div>

                    <div class="flex items-center space-x-3">

                        {{-- ── Dark Mode Toggle ── --}}
                        <button id="theme-toggle" onclick="toggleTheme()"
                                class="w-9 h-9 flex items-center justify-center rounded-xl
                                       border border-gray-200 dark:border-gray-700
                                       bg-gray-50 dark:bg-gray-800
                                       text-gray-500 dark:text-gray-400
                                       hover:bg-gray-100 dark:hover:bg-gray-700
                                       transition">
                            <svg id="icon-moon" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                            </svg>
                            <svg id="icon-sun" class="w-4 h-4 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707M17.657 17.657l-.707-.707M6.343 6.343l-.707-.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                            </svg>
                        </button>

                        {{-- Notifications --}}
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open"
                                    class="relative w-9 h-9 flex items-center justify-center rounded-xl
                                           border border-gray-200 dark:border-gray-700
                                           bg-gray-50 dark:bg-gray-800
                                           text-gray-500 dark:text-gray-400
                                           hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                                </svg>
                                @if(Auth::user()->unreadNotifications->count() > 0)
                                <span class="absolute -top-0.5 -right-0.5 w-4 h-4 bg-red-500 rounded-full text-xs text-white flex items-center justify-center">{{ Auth::user()->unreadNotifications->count() }}</span>
                                @endif
                            </button>
                            <div x-show="open" @click.away="open = false" style="display:none;"
                                 class="absolute right-0 mt-2 w-80
                                        bg-white dark:bg-gray-900
                                        border border-gray-200 dark:border-gray-700
                                        rounded-xl shadow-lg overflow-hidden z-50">
                                <div class="p-4 border-b border-gray-100 dark:border-gray-800">
                                    <h3 class="font-semibold text-gray-800 dark:text-gray-100">Notifikasi</h3>
                                </div>
                                <div class="max-h-80 overflow-y-auto">
                                    @forelse(Auth::user()->notifications->take(5) as $notification)
                                    <a href="{{ route('admin.notifications.index') }}"
                                       class="block p-4 border-b border-gray-100 dark:border-gray-800
                                              hover:bg-gray-50 dark:hover:bg-gray-800
                                              {{ $notification->read_at ? '' : 'bg-blue-50 dark:bg-blue-950/40' }}">
                                        <p class="text-sm text-gray-700 dark:text-gray-300">{{ $notification->data['message'] ?? 'Notifikasi baru' }}</p>
                                        <p class="text-xs text-gray-400 mt-1">{{ $notification->created_at->diffForHumans() }}</p>
                                    </a>
                                    @empty
                                    <div class="p-4 text-center text-gray-400 text-sm">Tidak ada notifikasi</div>
                                    @endforelse
                                </div>
                                <a href="{{ route('admin.notifications.index') }}"
                                   class="block p-3 text-center text-sm text-teal-600 dark:text-teal-400
                                          hover:bg-gray-50 dark:hover:bg-gray-800 border-t border-gray-100 dark:border-gray-800">
                                    Lihat semua
                                </a>
                            </div>
                        </div>

                        {{-- Profile --}}
                        <div class="hidden md:block relative" x-data="{ open: false }">
                            <button @click="open = !open"
                                    class="flex items-center space-x-3 pl-3 border-l border-gray-200 dark:border-gray-700">
                                <div class="text-right hidden lg:block">
                                    <p class="text-sm font-semibold text-gray-800 dark:text-gray-100">{{ Auth::user()->name }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">Administrator</p>
                                </div>
                                <div class="w-9 h-9 bg-teal-600 rounded-full flex items-center justify-center">
                                    <span class="text-white font-semibold text-sm">{{ substr(Auth::user()->name, 0, 1) }}</span>
                                </div>
                            </button>
                            <div x-show="open" @click.away="open = false" style="display:none;"
                                 class="absolute right-0 mt-2 w-48
                                        bg-white dark:bg-gray-900
                                        border border-gray-200 dark:border-gray-700
                                        rounded-xl shadow-lg overflow-hidden z-50">
                                <a href="{{ route('admin.profile.edit') }}"
                                   class="flex items-center gap-2 px-4 py-2.5 text-sm
                                          text-gray-700 dark:text-gray-300
                                          hover:bg-gray-50 dark:hover:bg-gray-800">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                    Profil
                                </a>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit"
                                            class="w-full flex items-center gap-2 px-4 py-2.5 text-sm
                                                   text-red-500 dark:text-red-400
                                                   hover:bg-red-50 dark:hover:bg-red-900/30">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                        Logout
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            {{-- Main --}}
            <main class="flex-1 overflow-y-auto mt-16 mb-16 bg-gray-50 dark:bg-gray-950">
                <div class="p-6 space-y-6">
                    @if(session('success'))
                    <div class="bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-700 text-green-800 dark:text-green-300 px-4 py-3 rounded-xl text-sm">
                        {{ session('success') }}
                    </div>
                    @endif
                    @if(session('error'))
                    <div class="bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-700 text-red-800 dark:text-red-300 px-4 py-3 rounded-xl text-sm">
                        {{ session('error') }}
                    </div>
                    @endif
                    @yield('content')
                </div>
            </main>

            {{-- Footer --}}
            <footer class="bg-white dark:bg-gray-900 border-t border-gray-200 dark:border-gray-800
                           fixed bottom-0 right-0 left-0 lg:left-64 z-30">
                <div class="px-6 py-3">
                    <p class="text-center text-gray-500 dark:text-gray-500 text-xs">
                        © {{ date('Y') }} BPVP Banda Aceh. All rights reserved.
                    </p>
                </div>
            </footer>
        </div>
    </div>

    <script>
    // ── Theme Toggle ──
    function toggleTheme() {
        const html  = document.getElementById('html-root');
        const isDark = html.classList.toggle('dark');
        localStorage.setItem('theme', isDark ? 'dark' : 'light');
        updateThemeIcons(isDark);
    }

    function updateThemeIcons(isDark) {
        document.getElementById('icon-moon').classList.toggle('hidden', isDark);
        document.getElementById('icon-sun').classList.toggle('hidden', !isDark);
    }

    // Inisialisasi ikon saat halaman load
    document.addEventListener('DOMContentLoaded', () => {
        updateThemeIcons(document.getElementById('html-root').classList.contains('dark'));
    });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
    $(document).ready(function () {
        $('select[name="program_pelatihan_unit_id"]').select2({
            placeholder: "Cari nama unit kompetensi...",
            allowClear: true,
            width: '100%',
            minimumInputLength: 2
        });
    });
    </script>
</body>
</html>