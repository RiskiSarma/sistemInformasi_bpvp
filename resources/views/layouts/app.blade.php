<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- <title>{{ config('app.name', 'BPVP Banda Aceh') }} - @yield('title', 'Dashboard')</title> --}}
    <title>BPVP Banda Aceh - Sistem Informasi</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link rel="icon" type="image/png" href="{{ asset('assets/img/logo blk banda.png') }}">

    <!-- Scripts -->
    {{-- @vite(['resources/css/app.css', 'resources/js/app.js']) --}}
    <!-- Tailwind via CDN (untuk development cepat) -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Font Awesome (kalau butuh icon) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="font-sans antialiased bg-gray-50" x-data="{ sidebarOpen: false, profileOpen: false, notificationOpen: false }">
    <div class="min-h-screen flex">
        <!-- Sidebar - Fixed -->
        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" 
                class="fixed inset-y-0 left-0 z-50 w-64 bg-white shadow-xl transform transition-transform duration-300 ease-in-out lg:translate-x-0 flex flex-col">
            
            <!-- Logo - Fixed at Top -->
            <div class="flex items-center justify-between h-16 px-6 border-b flex-shrink-0">
                <div class="flex items-center space-x-3">
                    <img src="https://blkaceh.kemnaker.go.id/wp-content/uploads/2023/07/cropped-logo-bpvp.png" alt="Logo BPVP" class="h-10 w-auto" onerror="this.src='https://blogger.googleusercontent.com/img/b/R29vZ2xl/AVvXsEjz9kmrBxqmtTcjR5DfGbcL0blmphH6V9chaKj5rJHWVW59vuEbp8OvusBJxR79eKcNEjUIstpT4gjQbVUSA5LgemfC5oy5hZgzsqxw8O3pg-064l2YToAxL9E2ljEPBHU05J_2Cl8roOI/s705/logo_blk_biru.png.png'">
                    <span class="text-sm font-bold text-gray-800">BPVP Banda Aceh</span>
                </div>
                <button @click="sidebarOpen = false" class="lg:hidden text-gray-500 hover:text-gray-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <!-- Navigation - Scrollable Area -->
            <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto" 
                x-data="{ 
                    programOpen: {{ 
                        request()->routeIs('admin.programs.index') || 
                        request()->routeIs('admin.programs.create') || 
                        request()->routeIs('admin.programs.edit') || 
                        request()->routeIs('admin.programs.show') || 
                        request()->routeIs('admin.programs.master') || 
                        request()->routeIs('admin.programs.units') 
                        ? 'true' : 'false' 
                    }}
                }">

                <!-- Dashboard -->
                <a href="{{ route('dashboard') }}" 
                class="flex items-center space-x-3 px-4 py-3 rounded-lg transition {{ request()->routeIs('dashboard') || request()->routeIs('admin.dashboard') ? 'bg-blue-600 text-white shadow-md' : 'text-gray-700 hover:bg-gray-100' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    <span class="font-medium">Dashboard</span>
                </a>

                <!-- Program Pelatihan - Dropdown -->
                <div>
                    <!-- Parent: Hanya hover, TIDAK PERNAH aktif biru solid -->
                    <button @click="programOpen = !programOpen" 
                            class="w-full flex items-center justify-between px-4 py-3 rounded-lg transition text-gray-700 hover:bg-gray-100">
                        <div class="flex items-center space-x-3">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                            <span class="font-medium">Program Pelatihan</span>
                        </div>
                        <svg class="w-4 h-4 transition-transform" :class="programOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>

                    <!-- Submenu -->
                    <div x-show="programOpen" x-collapse class="mt-2 space-y-1 pl-4">
                        <a href="{{ route('admin.programs.index') }}" 
                        class="block px-4 py-2 text-sm rounded-lg transition {{ request()->routeIs('admin.programs.index') || request()->routeIs('admin.programs.create') || request()->routeIs('admin.programs.edit') || request()->routeIs('admin.programs.show') ? 'bg-blue-600 text-white shadow-md' : 'text-gray-600 hover:bg-gray-100' }}">
                            Kelola Pelatihan
                        </a>
                        <a href="{{ route('admin.programs.master') }}" 
                        class="block px-4 py-2 text-sm rounded-lg transition {{ request()->routeIs('admin.programs.master') ? 'bg-blue-600 text-white shadow-md' : 'text-gray-600 hover:bg-gray-100' }}">
                            Program Pelatihan (Master)
                        </a>
                        <a href="{{ route('admin.programs.units') }}" 
                        class="block px-4 py-2 text-sm rounded-lg transition {{ request()->routeIs('admin.programs.units') ? 'bg-blue-600 text-white shadow-md' : 'text-gray-600 hover:bg-gray-100' }}">
                            Unit Kompetensi
                        </a>
                    </div>
                </div>

                <!-- Menu lainnya (sama seperti sebelumnya, tapi konsisten) -->
                <a href="{{ route('admin.participants.index') }}" 
                class="flex items-center space-x-3 px-4 py-3 rounded-lg transition {{ request()->routeIs('admin.participants.*') ? 'bg-blue-600 text-white shadow-md' : 'text-gray-700 hover:bg-gray-100' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                    <span class="font-medium">Peserta</span>
                </a>

                <a href="{{ route('admin.instructors.index') }}" 
                class="flex items-center space-x-3 px-4 py-3 rounded-lg transition {{ request()->routeIs('admin.instructors.*') ? 'bg-blue-600 text-white shadow-md' : 'text-gray-700 hover:bg-gray-100' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    <span class="font-medium">Instruktur</span>
                </a>

                <a href="{{ route('admin.attendance.index') }}" 
                class="flex items-center space-x-3 px-4 py-3 rounded-lg transition {{ request()->routeIs('admin.attendance.*') ? 'bg-blue-600 text-white shadow-md' : 'text-gray-700 hover:bg-gray-100' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <span class="font-medium">Kehadiran</span>
                </a>

                <a href="{{ route('admin.reports.index') }}" 
                class="flex items-center space-x-3 px-4 py-3 rounded-lg transition {{ request()->routeIs('admin.reports.*') ? 'bg-blue-600 text-white shadow-md' : 'text-gray-700 hover:bg-gray-100' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <span class="font-medium">Laporan</span>
                </a>

                <a href="{{ route('admin.certificates.index') }}" 
                class="flex items-center space-x-3 px-4 py-3 rounded-lg transition {{ request()->routeIs('admin.certificates.*') ? 'bg-blue-600 text-white shadow-md' : 'text-gray-700 hover:bg-gray-100' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <span class="font-medium">Sertifikat</span>
                </a>
            </nav>

            <!-- User Info - Fixed at Bottom -->
            <div class="flex-shrink-0 p-4 border-t bg-white">
                <div class="flex items-center space-x-3 px-4 py-3">
                    <div class="w-10 h-10 bg-blue-600 rounded-full flex items-center justify-center">
                        <span class="text-white font-semibold text-sm">{{ substr(Auth::user()->name, 0, 1) }}</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-gray-800 truncate">{{ Auth::user()->name }}</p>
                        <p class="text-xs text-gray-500 truncate">{{ Auth::user()->email }}</p>
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}" class="mt-2">
                    @csrf
                    <button type="submit" class="w-full px-4 py-2 text-sm text-red-600 hover:bg-red-50 rounded-lg transition flex items-center justify-center space-x-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                        <span>Logout</span>
                    </button>
                </form>
            </div>
        </aside>

        <!-- Overlay for mobile -->
        <div x-show="sidebarOpen" @click="sidebarOpen = false" x-transition:enter="transition-opacity ease-linear duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition-opacity ease-linear duration-300" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 z-40 bg-black bg-opacity-50 lg:hidden" style="display: none;"></div>

        <!-- Main Content Area - dengan margin untuk sidebar -->
        <div class="flex-1 flex flex-col lg:ml-64">
            <!-- Top Navbar - Fixed -->
            <header class="bg-white shadow-sm border-b fixed top-0 right-0 left-0 lg:left-64 z-30">
                <div class="flex items-center justify-between h-16 px-6">
                    <button @click="sidebarOpen = true" class="lg:hidden text-gray-500 hover:text-gray-700">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>
                    
                    <div class="flex-1 lg:flex-none">
                        <h2 class="text-xl font-semibold text-gray-800">@yield('title', 'Dashboard')</h2>
                    </div>

                    <div class="flex items-center space-x-4">
                        <!-- Notifications -->
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" class="relative text-gray-500 hover:text-gray-700">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                                </svg>
                                @if(Auth::user()->unreadNotifications->count() > 0)
                                <span class="absolute -top-1 -right-1 w-4 h-4 bg-red-500 rounded-full text-xs text-white flex items-center justify-center">{{ Auth::user()->unreadNotifications->count() }}</span>
                                @endif
                            </button>
                            
                            <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-lg border overflow-hidden z-50">
                                <div class="p-4 border-b">
                                    <h3 class="font-semibold">Notifikasi</h3>
                                </div>
                                <div class="max-h-96 overflow-y-auto">
                                    @forelse(Auth::user()->notifications->take(5) as $notification)
                                    <a href="{{ route('admin.notifications.index') }}" class="block p-4 hover:bg-gray-50 border-b {{ $notification->read_at ? 'bg-white' : 'bg-blue-50' }}">
                                        <p class="text-sm">{{ $notification->data['message'] ?? 'Notifikasi baru' }}</p>
                                        <p class="text-xs text-gray-500 mt-1">{{ $notification->created_at->diffForHumans() }}</p>
                                    </a>
                                    @empty
                                    <div class="p-4 text-center text-gray-500">Tidak ada notifikasi</div>
                                    @endforelse
                                </div>
                                <a href="{{ route('admin.notifications.index') }}" class="block p-3 text-center text-sm text-blue-600 hover:bg-gray-50 border-t">Lihat semua</a>
                            </div>
                        </div>
                        
                        <!-- Profile Dropdown -->
                        <div class="hidden md:block relative" x-data="{ open: false }">
                            <button @click="open = !open" class="flex items-center space-x-3 pl-4 border-l">
                                <div class="text-right">
                                    <p class="text-sm font-semibold text-gray-800">{{ Auth::user()->name }}</p>
                                    <p class="text-xs text-gray-500">Administrator</p>
                                </div>
                                <div class="w-10 h-10 bg-blue-600 rounded-full flex items-center justify-center">
                                    <span class="text-white font-semibold text-sm">{{ substr(Auth::user()->name, 0, 1) }}</span>
                                </div>
                            </button>
                            
                            <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border overflow-hidden z-50">
                                <a href="{{ route('admin.profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                    Profil
                                </a>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                        </svg>
                                        Logout
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Main Content Area - Scrollable dengan padding untuk navbar dan footer -->
            <main class="flex-1 overflow-y-auto mt-16 mb-16">
                <div class="p-6 space-y-6">
                    @if(session('success'))
                    <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg">
                        {{ session('success') }}
                    </div>
                    @endif

                    @if(session('error'))
                    <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">
                        {{ session('error') }}
                    </div>
                    @endif

                    @yield('content')
                </div>
            </main>

            <!-- Footer - Fixed at Bottom -->
            <footer class="bg-white border-t fixed bottom-0 right-0 left-0 lg:left-64 z-30">
                <div class="px-6 py-4">
                    <p class="text-center text-gray-600 text-sm">
                        © {{ date('Y') }} BPVP Banda Aceh. All rights reserved.
                    </p>
                </div>
            </footer>
        </div>
    </div>
</body>
</html>