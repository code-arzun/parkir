<x-layouts.app title="ParkirPWA - Dashboard Owner">

    <!-- SLOT HEADER -->
    <x-slot:header>
        <x-header 
            title="Dashboard Owner" 
            :subtitle="auth()->user()->name ?? 'Owner'" 
            icon="fa-chart-line" 
            iconColor="text-yellow-400"
            :showLogout="true"
        />
    </x-slot:header>

    <!-- NAVIGATION TABS (Murni 2 Tab) -->
    <x-slot:navigation>
        <div class="flex border-b border-slate-200 bg-white sticky top-0 z-20">
            <button onclick="switchTab('ringkasan')" id="tab-ringkasan" class="flex-1 py-3 text-center text-xs font-semibold active-tab flex justify-center items-center space-x-1">
                <i class="fa-solid fa-wallet"></i>
                <span>Ringkasan</span>
            </button>
            <button onclick="switchTab('transaksi')" id="tab-transaksi" class="flex-1 py-3 text-center text-xs font-semibold text-slate-500 flex justify-center items-center space-x-1">
                <i class="fa-solid fa-clock-rotate-left"></i>
                <span>Riwayat Transaksi</span>
            </button>
        </div>
    </x-slot:navigation>

    <!-- MAIN CONTENT AREA -->
    <div class="space-y-4">

        <!-- TAB 1: RINGKASAN OMSET -->
        <div id="section-ringkasan" class="space-y-3">
            <div class="bg-gradient-to-br from-blue-600 to-blue-800 text-white rounded-2xl p-4 shadow-md relative overflow-hidden">
                <div class="absolute -right-3 -bottom-3 text-white/10 text-7xl font-black">
                    <i class="fa-solid fa-wallet"></i>
                </div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-blue-200 block mb-1">Omset Hari Ini</span>
                <h2 class="text-2xl font-black mb-1">Rp {{ number_format($todayRevenue, 0, ',', '.') }}</h2>
                <span class="text-[10px] bg-emerald-500/30 text-emerald-200 font-semibold px-2 py-0.5 rounded-full border border-emerald-400/30 inline-flex items-center space-x-1">
                    <i class="fa-solid fa-circle-check"></i>
                    <span>Terbayar Lunas</span>
                </span>
            </div>

            <div class="grid grid-cols-2 gap-2.5">
                <div class="bg-slate-50 border border-slate-200 rounded-2xl p-3">
                    <div class="flex justify-between items-center mb-1">
                        <span class="text-[10px] font-bold text-slate-400 uppercase">Bulan Ini</span>
                        <div class="w-6 h-6 rounded-md bg-blue-100 text-blue-600 flex items-center justify-center text-[10px]">
                            <i class="fa-solid fa-calendar-check"></i>
                        </div>
                    </div>
                    <h3 class="text-sm font-black text-slate-800">Rp {{ number_format($monthRevenue, 0, ',', '.') }}</h3>
                    <p class="text-[9px] text-slate-400 mt-0.5">{{ date('F Y') }}</p>
                </div>

                <div class="bg-slate-50 border border-slate-200 rounded-2xl p-3">
                    <div class="flex justify-between items-center mb-1">
                        <span class="text-[10px] font-bold text-slate-400 uppercase">Selesai Hari Ini</span>
                        <div class="w-6 h-6 rounded-md bg-indigo-100 text-indigo-600 flex items-center justify-center text-[10px]">
                            <i class="fa-solid fa-flag-checkered"></i>
                        </div>
                    </div>
                    <h3 class="text-sm font-black text-slate-800">{{ $completedTodayCount }} <span class="text-[10px] font-normal text-slate-500">Unit</span></h3>
                    <p class="text-[9px] text-slate-400 mt-0.5">Check-out hari ini</p>
                </div>
            </div>

            <div class="bg-amber-50/80 border border-amber-200 rounded-2xl p-3.5 flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-amber-500 text-white rounded-xl flex items-center justify-center font-bold text-lg shadow-sm">
                        <i class="fa-solid fa-square-p"></i>
                    </div>
                    <div>
                        <span class="text-[10px] font-bold text-amber-800 uppercase block">Kendaraan Terparkir Aktif</span>
                        <h3 class="text-lg font-black text-amber-900 leading-tight">{{ $activeVehiclesCount }} Unit</h3>
                        <p class="text-[10px] text-amber-700 font-medium">{{ $activeMotor }} Motor • {{ $activeSepeda }} Sepeda</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- TAB 2: RIWAYAT TRANSAKSI -->
        <div id="section-transaksi" class="hidden space-y-3">
            <div class="flex justify-between items-center px-1">
                <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">10 Transaksi Terakhir</span>
                <button onclick="window.location.reload()" class="text-[11px] font-bold text-blue-600 flex items-center space-x-1">
                    <i class="fa-solid fa-arrows-rotate"></i>
                    <span>Refresh</span>
                </button>
            </div>

            <div class="space-y-2">
                @forelse($recentTransactions as $item)
                    <div class="bg-white border border-slate-200 rounded-2xl p-3 shadow-sm space-y-2">
                        <div class="flex justify-between items-start border-b border-slate-100 pb-2">
                            <div class="flex items-center space-x-2">
                                <div class="w-8 h-8 rounded-lg flex items-center justify-center text-xs font-bold {{ $item->vehicle->vehicle_type === 'motor' ? 'bg-blue-50 text-blue-600' : 'bg-emerald-50 text-emerald-600' }}">
                                    <i class="fa-solid {{ $item->vehicle->vehicle_type === 'motor' ? 'fa-motorcycle' : 'fa-bicycle' }}"></i>
                                </div>
                                <div>
                                    <h4 class="text-xs font-black text-slate-800 leading-tight">{{ $item->vehicle->license_plate }}</h4>
                                    <p class="text-[9px] text-slate-400">Petugas: {{ $item->attendant->name ?? 'Petugas' }}</p>
                                </div>
                            </div>

                            <div>
                                @if($item->status === 'active')
                                    <span class="bg-amber-100 text-amber-800 text-[9px] font-bold px-2 py-0.5 rounded-full border border-amber-200">
                                        Parkir Aktif
                                    </span>
                                @else
                                    <div class="text-right">
                                        <span class="text-xs font-black text-emerald-600 block">Rp {{ number_format($item->total_fee, 0, ',', '.') }}</span>
                                        <span class="text-[9px] text-slate-400 font-semibold uppercase">{{ $item->payment_method }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-1 text-[10px] text-slate-500 bg-slate-50 rounded-lg p-2 border border-slate-100">
                            <div>
                                <span class="text-[9px] text-slate-400 block">Masuk:</span>
                                <span class="font-bold text-slate-700">{{ \Carbon\Carbon::parse($item->check_in_time)->format('d/m/Y H:i') }}</span>
                            </div>
                            <div>
                                <span class="text-[9px] text-slate-400 block">Keluar:</span>
                                <span class="font-bold text-slate-700">{{ $item->check_out_time ? \Carbon\Carbon::parse($item->check_out_time)->format('d/m/Y H:i') : '-' }}</span>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8 bg-slate-50 border border-slate-200 rounded-2xl">
                        <i class="fa-solid fa-inbox text-slate-300 text-2xl mb-1 block"></i>
                        <p class="text-xs text-slate-400">Belum ada transaksi parkir yang tercatat.</p>
                    </div>
                @endforelse
            </div>
        </div>

    </div>

    <!-- SCRIPTS -->
    <x-slot:scripts>
        <script>
            function switchTab(tab) {
                document.getElementById('section-ringkasan').classList.add('hidden');
                document.getElementById('section-transaksi').classList.add('hidden');

                document.getElementById('tab-ringkasan').classList.remove('active-tab', 'text-blue-600');
                document.getElementById('tab-transaksi').classList.remove('active-tab', 'text-blue-600');

                if (tab === 'ringkasan') {
                    document.getElementById('section-ringkasan').classList.remove('hidden');
                    document.getElementById('tab-ringkasan').classList.add('active-tab');
                } else {
                    document.getElementById('section-transaksi').classList.remove('hidden');
                    document.getElementById('tab-transaksi').classList.add('active-tab');
                }
            }
        </script>
    </x-slot:scripts>

</x-layouts.app>