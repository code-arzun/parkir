@props([
    'title' => 'ParkirPWA',
    'subtitle' => '',
    'icon' => 'fa-square-p',
    'iconColor' => 'text-yellow-400',
    'showLogout' => false,
    'badgeText' => null,
])

<!-- TOP NAVBAR -->
<div class="bg-slate-900 text-white px-4 py-3 flex justify-between items-center shadow-md relative z-30">
    <div class="flex items-center space-x-3">
        <!-- Tombol Hamburger Menu -->
        <button type="button" onclick="toggleSidebar()" class="w-9 h-9 flex items-center justify-center rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white transition-all border border-slate-700">
            <i class="fa-solid fa-bars text-base"></i>
        </button>

        <div class="flex items-center space-x-2">
            <i class="fa-solid {{ $icon }} {{ $iconColor }} text-lg"></i>
            <div>
                <h1 class="text-xs font-bold leading-none">{{ $title }}</h1>
                @if($subtitle)
                    <p class="text-[9px] text-slate-400 mt-0.5">{{ $subtitle }}</p>
                @endif
            </div>
        </div>
    </div>

    <div class="flex items-center space-x-2">
        @if($badgeText)
            <span class="bg-emerald-500/20 text-emerald-400 text-[10px] font-bold px-2 py-0.5 rounded-full border border-emerald-500/30 flex items-center space-x-1">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                <span>{{ $badgeText }}</span>
            </span>
        @endif

        @if($showLogout)
            <form action="{{ route('logout') }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="bg-red-500/20 hover:bg-red-600 text-red-400 hover:text-white text-[10px] font-bold px-2.5 py-1.5 rounded-xl transition-all border border-red-500/30 flex items-center space-x-1">
                    <i class="fa-solid fa-right-from-bracket"></i>
                </button>
            </form>
        @endif
    </div>
</div>

<!-- SIDEBAR OVERLAY & DRAWER -->
<div id="sidebar-overlay" onclick="toggleSidebar()" class="hidden fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-40 transition-opacity"></div>

<aside id="sidebar-drawer" class="fixed top-0 left-0 bottom-0 w-64 bg-slate-900 text-white z-50 transform -translate-x-full transition-transform duration-300 ease-in-out flex flex-col justify-between shadow-2xl">
    <div>
        <!-- Sidebar Header -->
        <div class="p-4 border-b border-slate-800 flex justify-between items-center bg-slate-950">
            <div class="flex items-center space-x-2.5">
                <div class="w-8 h-8 rounded-lg bg-blue-600 flex items-center justify-center font-black text-white text-sm">
                    <i class="fa-solid fa-square-p"></i>
                </div>
                <div>
                    <h2 class="text-xs font-black tracking-wider uppercase">ParkirPWA</h2>
                    <p class="text-[9px] text-slate-400">{{ auth()->user()->name ?? 'Pengguna' }}</p>
                </div>
            </div>
            <button onclick="toggleSidebar()" class="text-slate-400 hover:text-white p-1">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        <!-- Navigation Links -->
        <nav class="p-3 space-y-1 text-xs font-bold">
            <!-- Link POS Penjaga (Akses Utama) -->
            <a href="{{ route('attendant.app') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl hover:bg-slate-800 text-slate-300 hover:text-white transition-all {{ request()->routeIs('attendant.app') ? 'bg-blue-600 text-white font-black' : '' }}">
                <i class="fa-solid fa-cash-register w-5 text-center text-emerald-400"></i>
                <span>POS Penjaga</span>
            </a>

            <!-- Menu Khusus Owner -->
            @if(auth()->check() && auth()->user()->role === 'owner')
                <div class="pt-3 pb-1 px-3 text-[9px] font-bold text-slate-500 uppercase tracking-wider">
                    Menu Owner
                </div>

                <a href="{{ route('owner.dashboard') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl hover:bg-slate-800 text-slate-300 hover:text-white transition-all {{ request()->routeIs('owner.dashboard') ? 'bg-blue-600 text-white font-black' : '' }}">
                    <i class="fa-solid fa-chart-line w-5 text-center text-blue-400"></i>
                    <span>Dashboard</span>
                </a>

                <a href="{{ route('owner.rates') }}?tab=tarif" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl hover:bg-slate-800 text-slate-300 hover:text-white transition-all">
                    <i class="fa-solid fa-sliders w-5 text-center text-amber-400"></i>
                    <span>Tarif</span>
                </a>
            @endif

            <div class="pt-3 pb-1 px-3 text-[9px] font-bold text-slate-500 uppercase tracking-wider">
                Portal Pengendara
            </div>

            <a href="{{ route('pengguna.app') }}" target="_blank" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl hover:bg-slate-800 text-slate-300 hover:text-white transition-all">
                <i class="fa-solid fa-ticket w-5 text-center text-purple-400"></i>
                <span>Portal Tiket Pengguna</span>
            </a>
        </nav>
    </div>

    <!-- Sidebar Footer / Logout -->
    <div class="p-3 border-t border-slate-800 bg-slate-950">
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="w-full bg-red-600/20 hover:bg-red-600 text-red-400 hover:text-white text-xs font-bold py-2.5 px-3 rounded-xl transition-all border border-red-500/30 flex items-center justify-center space-x-2">
                <i class="fa-solid fa-right-from-bracket"></i>
                <span>Keluar dari Sistem</span>
            </button>
        </form>
    </div>
</aside>

<script>
    function toggleSidebar() {
        const drawer = document.getElementById('sidebar-drawer');
        const overlay = document.getElementById('sidebar-overlay');
        
        if (drawer.classList.contains('-translate-x-full')) {
            drawer.classList.remove('-translate-x-full');
            overlay.classList.remove('hidden');
        } else {
            drawer.classList.add('-translate-x-full');
            overlay.classList.add('hidden');
        }
    }
</script>