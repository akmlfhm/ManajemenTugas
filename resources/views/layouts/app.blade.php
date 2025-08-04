<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Elearning Fakultas Teknik Informatika - Universitas Muhammadiyah Semarang</title>
    {{-- PERUBAHAN DI SINI: Tambahkan link untuk favicon Anda --}}
    <link rel="icon" href="{{ asset('image/logo.png') }}" type="image/png">

    <!-- Fonts: Menggunakan Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Icons: Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    <!-- Scripts & Styles: Menggunakan Vite (Cara Standar Laravel) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- AlpineJS untuk interaktivitas -->
    {{-- Plugin Persist untuk menyimpan state sidebar di localStorage --}}
    <script src="https://cdn.jsdelivr.net/npm/@alpinejs/persist@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f3f4f6;
        }
        [x-cloak] { display: none !important; }
        .transition-all { transition: all 0.3s ease-in-out; }
    </style>
</head>
<body class="bg-gray-100">

    {{-- 
        PENAMBAHAN LOGIKA SIDEBAR:
        - x-data mendefinisikan state awal sidebar.
        - $persist(true) akan menyimpan state 'sidebarOpen' di browser, jadi tidak akan reset saat refresh.
        - 'lg:w-64': sidebar akan memiliki lebar 64 di layar besar.
        - 'w-20': sidebar akan menyempit menjadi lebar 20 saat ditutup di layar besar.
    --}}
    <div x-data="{ sidebarOpen: $persist(true) }" class="flex h-screen bg-gray-200">
        
        <!-- Sidebar -->
        <aside 
            class="flex-shrink-0 bg-[#1e293b] text-white transition-all duration-300"
            :class="sidebarOpen ? 'w-64' : 'w-20'">
            
            <div class="flex flex-col h-full">
                {{-- PERUBAHAN: Mengganti teks dengan logo --}}
                <div class="flex items-center justify-center h-20 border-b border-gray-700 p-4">
                    <img src="{{ asset('image/logo-informatika.png') }}" alt="Univ Logo" 
                         class="transition-all duration-300"
                         :class="sidebarOpen ? 'max-h-12' : 'max-h-10'">
                </div>

                <nav class="flex-1 px-2 py-4 space-y-2">
                    <a href="{{ route('dashboard') }}" title="Dashboard" class="flex items-center justify-center lg:justify-start px-4 py-2.5 text-sm font-medium rounded-lg hover:bg-gray-700 {{ request()->routeIs('dashboard') ? 'bg-gray-700' : '' }}">
                        <i class="fas fa-home-alt w-6 text-center"></i>
                        <span class="ml-3" x-show="sidebarOpen">Dashboard</span>
                    </a>
                    <a href="{{ route('tasks.index') }}" title="Tugas" class="flex items-center justify-center lg:justify-start px-4 py-2.5 text-sm font-medium rounded-lg hover:bg-gray-700 {{ request()->routeIs('tasks.*') ? 'bg-gray-700' : '' }}">
                        <i class="fas fa-tasks w-6 text-center"></i>
                        <span class="ml-3" x-show="sidebarOpen">Tugas</span>
                    </a>

                    @if(Auth::user()->isAdmin())
                    <a href="{{ route('team.index') }}" title="Anggota Mahasiswa" class="flex items-center justify-center lg:justify-start px-4 py-2.5 text-sm font-medium rounded-lg hover:bg-gray-700 {{ request()->routeIs('team.*') ? 'bg-gray-700' : '' }}">
                        <i class="fas fa-users w-6 text-center"></i>
                        <span class="ml-3" x-show="sidebarOpen">Anggota Mahasiswa</span>
                    </a>
                    <a href="{{ route('analytics.index') }}" title="Analytics" class="flex items-center justify-center lg:justify-start px-4 py-2.5 text-sm font-medium rounded-lg hover:bg-gray-700 {{ request()->routeIs('analytics.*') ? 'bg-gray-700' : '' }}">
                        <i class="fas fa-chart-pie w-6 text-center"></i>
                        <span class="ml-3" x-show="sidebarOpen">Analytics</span>
                    </a>
                    @endif
                </nav>

                <div class="p-2 mt-auto border-t border-gray-700">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" title="Logout" class="w-full flex items-center justify-center lg:justify-start px-4 py-2.5 text-sm font-medium rounded-lg hover:bg-gray-700">
                            <i class="fas fa-sign-out-alt w-6 text-center"></i>
                            <span class="ml-3" x-show="sidebarOpen">Logout</span>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Header -->
            <header class="bg-white shadow-sm p-4 flex justify-between items-center">
                <div class="flex items-center gap-4">
                    <!-- Tombol Hamburger yang selalu ada -->
                    <button @click.stop="sidebarOpen = !sidebarOpen" class="text-gray-500 focus:outline-none">
                        <i class="fas fa-bars fa-lg"></i>
                    </button>
                    <!-- Judul Halaman dinamis -->
                    <h1 class="text-2xl font-bold text-gray-800">@yield('header', 'Dashboard')</h1>
                </div>
                
                <div class="flex items-center space-x-4">
                    @auth
                    <!-- User Profile Dropdown -->
                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open" class="focus:outline-none">
                            <img src="{{ Auth::user()->avatar ? asset('storage/' . Auth::user()->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->name) . '&background=60a5fa&color=FFFFFF' }}" alt="{{ Auth::user()->name }}" class="w-10 h-10 rounded-full object-cover">
                        </button>
                        
                        <div x-cloak x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-10 border">
                            <div class="px-4 py-2 text-sm text-gray-700 border-b">
                                <p class="font-semibold">{{ Auth::user()->name }}</p>
                                <p class="text-xs text-gray-500">{{ Auth::user()->isAdmin() ? 'Admin Role' : 'User Role' }}</p>
                            </div>
                            <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Profil Saya</a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full text-left block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Logout</button>
                            </form>
                        </div>
                    </div>
                    @endauth
                </div>
            </header>

            <!-- Main Content -->
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100 p-6">
                @yield('content')
            </main>

            <!-- Footer -->
            <footer class="py-5 px-4 bg-white text-center text-xs text-gray-500 border-t">
                <p>Copyright &copy; {{ date('Y') }} Arya Bintang Cahyono.</p>
            </footer>
        </div>
    </div>

    @stack('scripts')
</body>
</html>
