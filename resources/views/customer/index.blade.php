<x-layouts.app title="ParkirPWA - Portal Pengendara">

    <!-- MAIN CONTAINER -->
    <div class="space-y-4">

        <!-- ================= HEADER BANNER KUSTOM ================= -->
        <div class="bg-gradient-to-r from-blue-700 to-blue-600 -mx-4 -mt-4 p-5 rounded-b-3xl text-white shadow-md relative">
            <div class="flex justify-between items-center">
                <div class="flex items-center space-x-2">
                    <div class="w-8 h-8 bg-white/20 backdrop-blur-md rounded-xl flex items-center justify-center font-bold text-yellow-300">
                        <i class="fa-solid fa-square-p text-lg"></i>
                    </div>
                    <div>
                        <h1 class="text-sm font-black tracking-wide leading-tight">ParkirPWA</h1>
                        <p class="text-[9px] text-blue-100 font-medium" id="header-subtitle">Portal Pengendara</p>
                    </div>
                </div>

                <!-- Tombol Logout (Tampil Hanya Saat Sudah Login) -->
                <button id="btn-header-logout" onclick="handleCustomerLogout()" 
                    class="hidden bg-red-500/80 hover:bg-red-600 text-white text-[10px] font-bold px-3 py-1.5 rounded-xl backdrop-blur-md flex items-center space-x-1.5 transition-all shadow-sm">
                    <i class="fa-solid fa-right-from-bracket"></i>
                    <span>Keluar</span>
                </button>
            </div>

            <!-- Identitas User Saat Sudah Login -->
            <div id="header-user-info" class="hidden mt-3 pt-3 border-t border-white/10 flex justify-between items-end">
                <div>
                    <span class="text-[9px] text-blue-200 block font-bold uppercase tracking-wider">Selamat Datang</span>
                    <h2 class="text-base font-black text-white leading-tight" id="header-user-name">-</h2>
                </div>
                <span id="header-nopol-badge" class="bg-white/20 backdrop-blur-md text-yellow-300 text-[10px] font-black px-2.5 py-1 rounded-lg border border-white/20">
                    -
                </span>
            </div>
        </div>


        <!-- ================= SECTION 1: FORM LOGIN PENGENDARA ================= -->
        <div id="section-login" class="bg-white rounded-2xl shadow-sm p-5 border border-slate-200">
            <div class="text-center mb-5">
                <div class="w-12 h-12 bg-blue-50 rounded-2xl flex items-center justify-center mx-auto mb-2 text-blue-600 font-bold">
                    <i class="fa-solid fa-right-to-bracket text-xl"></i>
                </div>
                <h3 class="text-sm font-black text-slate-800">Masuk Akun Pengendara</h3>
                <p class="text-[10px] text-slate-400 mt-0.5">Gunakan Nopol Kendaraan & Password/PIN Anda</p>
            </div>

            <form id="form-login-customer" onsubmit="handleCustomerLogin(event)" class="space-y-3">
                <div>
                    <label class="block text-[10px] font-bold text-slate-600 uppercase mb-1">Nomor Polisi / ID Sepeda</label>
                    <input type="text" id="login-nopol" required placeholder="Contoh: B1234XYZ"
                        class="w-full px-4 py-3 bg-slate-50 border border-slate-300 rounded-xl text-sm font-black uppercase tracking-wider focus:bg-white focus:ring-2 focus:ring-blue-600 focus:outline-none">
                </div>

                <div>
                    <div class="relative">
                        <input type="password" id="login-password" required placeholder="Password"
                            class="w-full pl-4 pr-10 py-3 bg-slate-50 border border-slate-300 rounded-xl text-xs font-bold focus:bg-white focus:ring-2 focus:ring-blue-600 focus:outline-none">
                        <button type="button" onclick="togglePasswordVisibility('login-password', 'icon-login-eye')" 
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 p-1 text-xs">
                            <i id="icon-login-eye" class="fa-solid fa-eye"></i>
                        </button>
                    </div>
                    <div>
                        <p class="text-[9px] text-slate-400 mt-1">*Pertama kali masuk? Gunakan Nopol sebagai password default.</p>
                    </div>
                </div>

                <button type="submit" id="btn-login" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3.5 rounded-xl shadow-lg shadow-blue-600/30 transition-all text-xs flex items-center justify-center space-x-2">
                    <i class="fa-solid fa-right-to-bracket"></i>
                    <span>MASUK</span>
                </button>
            </form>
        </div>


        <!-- ================= SECTION 2: MODAL WAKTU PERTAMA KALI LOGIN ================= -->
        <div id="section-change-password-first" class="hidden bg-amber-50 border-2 border-amber-400 rounded-2xl p-5 space-y-3 shadow-xl">
            <div class="text-center">
                <div class="w-10 h-10 bg-amber-500 text-white rounded-xl flex items-center justify-center mx-auto mb-1 text-lg font-bold">
                    <i class="fa-solid fa-user-shield"></i>
                </div>
                <h3 class="text-xs font-black text-amber-900 uppercase">Lengkapi Profil & Keamanan</h3>
                <p class="text-[10px] text-amber-800 mt-0.5">Silakan lengkapi data diri Anda & buat Password/PIN baru untuk melanjutkan.</p>
            </div>

            <form onsubmit="handleFirstPasswordChange(event)" class="space-y-3 bg-white p-4 rounded-xl border border-amber-200">
                <div>
                    <label class="block text-[10px] font-bold text-slate-600 uppercase mb-1">Nama Lengkap</label>
                    <input type="text" id="first-driver-name" required placeholder="Contoh: Budi Santoso"
                        class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs font-bold focus:bg-white focus:outline-none">
                </div>

                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block text-[10px] font-bold text-slate-600 uppercase mb-1">No. WhatsApp</label>
                        <input type="tel" id="first-wa-number" required minlength="9" maxlength="13" inputmode="numeric" placeholder="08xxxxxxxxxx"
                            class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs font-bold focus:bg-white focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-600 uppercase mb-1">Kepemilikan</label>
                        <select id="first-ownership" class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs font-bold focus:bg-white focus:outline-none">
                            <option value="pribadi">Pribadi</option>
                            <option value="keluarga">Keluarga</option>
                            <option value="teman">Teman / Pinjam</option>
                            <option value="kantor">Kantor</option>
                        </select>
                    </div>
                </div>

                <div class="relative">
                    <input type="password" id="first-new-password" required minlength="4" placeholder="Minimal 4 karakter"
                        class="w-full pl-3 pr-10 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs font-bold focus:bg-white focus:outline-none">
                    <button type="button" onclick="togglePasswordVisibility('first-new-password', 'icon-first-eye')" 
                        class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 p-1 text-xs">
                        <i id="icon-first-eye" class="fa-solid fa-eye"></i>
                    </button>
                </div>

                <button type="submit" class="w-full bg-amber-600 hover:bg-amber-700 text-white font-bold py-3 rounded-xl text-xs shadow-md shadow-amber-600/30 transition-all">
                    SIMPAN & AMBIL TIKET
                </button>
            </form>
        </div>


        <!-- ================= SECTION 3: DASHBOARD PENGENDARA (SISTEM TAB) ================= -->
        <div id="section-dashboard" class="hidden space-y-4">

            <!-- TAB 1: TIKET QR CODE & SESI PARKIR AKTIF -->
            <div id="tab-content-ticket" class="space-y-4">
                <!-- CARD QR CODE -->
                <div class="bg-white rounded-2xl shadow-sm p-5 text-center border border-slate-200 relative">
                    <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Tiket QR Code Anda</span>
                    <h2 class="text-2xl font-black text-slate-800 tracking-tight mb-3" id="display-nopol">-</h2>

                    <div class="bg-slate-50 p-4 rounded-2xl inline-block border border-slate-200 mb-2 shadow-inner">
                        <div id="qrcode" class="flex justify-center"></div>
                    </div>

                    <p class="text-[10px] text-slate-500 font-medium">Tunjukkan QR Code ini kepada penjaga parkir saat <span class="text-blue-600 font-bold">Check-In</span> atau <span class="text-blue-600 font-bold">Check-Out</span>.</p>
                </div>

                <!-- SESI PARKIR AKTIF (LAYOUT BARU) -->
                <div id="active-session-card" class="hidden bg-amber-50 border border-amber-200 rounded-2xl p-4 space-y-2.5 shadow-sm">
                    <div class="flex justify-between items-center border-b border-amber-200/60 pb-2">
                        <div class="flex items-center space-x-2">
                            <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                            <span class="text-[10px] font-bold text-amber-900 uppercase tracking-wider">Sedang Terparkir Aktif</span>
                        </div>
                        <span id="active-payment-badge" class="text-[9px] font-bold px-2.5 py-0.5 rounded-full"></span>
                    </div>

                    <!-- 1. BARIS ATAS: LOKASI (FULL WIDTH) -->
                    <div class="bg-white/80 backdrop-blur-sm p-3 rounded-xl border border-amber-200/60 text-center">
                        <span class="text-[9px] text-amber-800/80 block font-bold uppercase tracking-wider">Lokasi Parkir</span>
                        <span class="text-xs font-black text-amber-950 block truncate mt-0.5" id="active-location-name">-</span>
                    </div>

                   <!-- 2. BARIS TENGAH: WAKTU MASUK (3 KOLOM: HARI, TANGGAL, JAM) -->
                    <div class="grid grid-cols-3 gap-2 text-center">
                        <div class="bg-white/80 backdrop-blur-sm p-2 rounded-xl border border-amber-200/60">
                            <span class="text-[9px] text-amber-800/80 block font-bold uppercase tracking-wider">Hari</span>
                            <span class="text-xs font-bold text-slate-800 block mt-0.5 capitalize" id="active-checkin-day">-</span>
                        </div>
                        <div class="bg-white/80 backdrop-blur-sm p-2 rounded-xl border border-amber-200/60">
                            <span class="text-[9px] text-amber-800/80 block font-bold uppercase tracking-wider">Tanggal</span>
                            <span class="text-xs font-bold text-slate-800 block mt-0.5" id="active-checkin-date">-</span>
                        </div>
                        <div class="bg-white/80 backdrop-blur-sm p-2 rounded-xl border border-amber-200/60">
                            <span class="text-[9px] text-amber-800/80 block font-bold uppercase tracking-wider">Jam Masuk</span>
                            <span class="text-xs font-bold text-slate-800 block mt-0.5" id="active-checkin-clock">-</span>
                        </div>
                    </div>

                    <!-- 3. BARIS BAWAH: DURASI & BIAYA (2 KOLOM) -->
                    <div class="grid grid-cols-2 gap-2 text-center">
                        <div class="bg-white/80 backdrop-blur-sm p-2.5 rounded-xl border border-amber-200/60">
                            <span class="text-[9px] text-amber-800/80 block font-bold uppercase tracking-wider">Durasi Parkir</span>
                            <span class="text-xs font-black text-amber-950 block mt-0.5" id="active-duration">-</span>
                        </div>
                        <div class="bg-white/80 backdrop-blur-sm p-2.5 rounded-xl border border-amber-200/60">
                            <span class="text-[9px] text-amber-800/80 block font-bold uppercase tracking-wider">Estimasi Tarif</span>
                            <span class="text-xs font-black text-blue-600 block mt-0.5" id="active-fee">Rp 0</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TAB 2: RIWAYAT TRANSAKSI PARKIR -->
            <div id="tab-content-history" class="hidden space-y-3">
                <div class="bg-white rounded-2xl shadow-sm p-4 border border-slate-200 space-y-3">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                        <div class="flex items-center space-x-2">
                            <i class="fa-solid fa-clock-rotate-left text-blue-600 text-xs"></i>
                            <h4 class="text-xs font-bold text-slate-800">Riwayat Transaksi Parkir</h4>
                        </div>
                    </div>
                    <div id="history-container" class="space-y-2"></div>
                </div>
            </div>

            <!-- TAB 3: PROFIL & KEAMANAN AKUN -->
            <div id="tab-content-profile" class="hidden space-y-4">
                <!-- FORM PROFIL -->
                <div class="bg-white rounded-2xl shadow-sm p-4 border border-slate-200 space-y-3">
                    <div class="flex items-center space-x-2 border-b border-slate-100 pb-2">
                        <i class="fa-solid fa-user-gear text-blue-600 text-xs"></i>
                        <h4 class="text-xs font-bold text-slate-800">Data Pengendara</h4>
                    </div>

                    <form onsubmit="handleUpdateProfile(event)" class="space-y-3">
                        <div>
                            <label class="block text-[10px] font-bold text-slate-600 uppercase mb-1">Nama Lengkap</label>
                            <input type="text" id="prof-name" required placeholder="Nama Anda"
                                class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs font-bold focus:bg-white focus:outline-none">
                        </div>

                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="block text-[10px] font-bold text-slate-600 uppercase mb-1">No. WhatsApp</label>
                                <input type="tel" id="prof-wa" required minlength="9" maxlength="13" inputmode="numeric" placeholder="08xxxxxxxxxx"
                                    class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs font-bold focus:bg-white focus:outline-none">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-600 uppercase mb-1">Kepemilikan</label>
                                <select id="prof-ownership" class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs font-bold focus:bg-white focus:outline-none">
                                    <option value="pribadi">Pribadi</option>
                                    <option value="keluarga">Keluarga</option>
                                    <option value="teman">Teman / Pinjam</option>
                                    <option value="kantor">Kantor</option>
                                </select>
                            </div>
                        </div>

                        <button type="submit" class="w-full bg-slate-900 hover:bg-slate-800 text-white font-bold py-2.5 rounded-xl text-xs transition-all">
                            SIMPAN PROFIL
                        </button>
                    </form>
                </div>

                <!-- FORM GANTI PASSWORD AKUN -->
                <div class="bg-white rounded-2xl shadow-sm p-4 border border-slate-200 space-y-3">
                    <div class="flex items-center space-x-2 border-b border-slate-100 pb-2">
                        <i class="fa-solid fa-key text-blue-600 text-xs"></i>
                        <h4 class="text-xs font-bold text-slate-800">Ubah Password / PIN</h4>
                    </div>

                    <form onsubmit="handleChangePasswordOptional(event)" class="space-y-3">
                        <div class="relative">
                            <input type="password" id="opt-new-password" required minlength="4" placeholder="Minimal 4 karakter"
                                class="w-full pl-3 pr-10 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs font-bold focus:bg-white focus:outline-none">
                            <button type="button" onclick="togglePasswordVisibility('opt-new-password', 'icon-opt-eye')" 
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 p-1 text-xs">
                                <i id="icon-opt-eye" class="fa-solid fa-eye"></i>
                            </button>
                        </div>

                        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2.5 rounded-xl text-xs transition-all shadow-md shadow-blue-600/30">
                            PERBARUI PASSWORD
                        </button>
                    </form>
                </div>
            </div>

        </div>

    </div>

    <!-- ================= BOTTOM NAVIGATION BAR ================= -->
    <div id="customer-bottom-nav" class="hidden fixed bottom-0 left-0 right-0 max-w-md mx-auto bg-white border-t border-slate-200 px-6 py-2 z-30 flex justify-around items-center shadow-lg">
        <button onclick="switchCustomerTab('ticket')" id="nav-btn-ticket" class="flex flex-col items-center text-blue-600 font-bold transition-all">
            <i class="fa-solid fa-qrcode text-lg mb-0.5"></i>
            <span class="text-[10px]">Tiket QR</span>
        </button>
        <button onclick="switchCustomerTab('history')" id="nav-btn-history" class="flex flex-col items-center text-slate-400 hover:text-slate-600 font-bold transition-all">
            <i class="fa-solid fa-clock-rotate-left text-lg mb-0.5"></i>
            <span class="text-[10px]">Riwayat</span>
        </button>
        <button onclick="switchCustomerTab('profile')" id="nav-btn-profile" class="flex flex-col items-center text-slate-400 hover:text-slate-600 font-bold transition-all">
            <i class="fa-solid fa-user-gear text-lg mb-0.5"></i>
            <span class="text-[10px]">Profil</span>
        </button>
    </div>

    <!-- SCRIPTS LOGIC -->
    <x-slot:scripts>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>

        <script>
            const CUSTOMER_KEY = 'parkir_pwa_customer_session';

            document.addEventListener('DOMContentLoaded', () => {
                const savedSession = localStorage.getItem(CUSTOMER_KEY);
                if (savedSession) {
                    try {
                        const user = JSON.parse(savedSession);
                        if (user.is_first_login) {
                            showFirstChangePasswordScreen(user);
                        } else {
                            loadDashboardData(user.vehicle_id);
                        }
                    } catch (e) {
                        localStorage.removeItem(CUSTOMER_KEY);
                    }
                }
            });

            // 0. Switch Customer Tab Navigation
            function switchCustomerTab(tabName) {
                const tabTicket = document.getElementById('tab-content-ticket');
                const tabHistory = document.getElementById('tab-content-history');
                const tabProfile = document.getElementById('tab-content-profile');

                const btnTicket = document.getElementById('nav-btn-ticket');
                const btnHistory = document.getElementById('nav-btn-history');
                const btnProfile = document.getElementById('nav-btn-profile');

                // Hide All
                tabTicket.classList.add('hidden');
                tabHistory.classList.add('hidden');
                tabProfile.classList.add('hidden');

                // Reset Button Styles
                [btnTicket, btnHistory, btnProfile].forEach(btn => {
                    btn.className = "flex flex-col items-center text-slate-400 hover:text-slate-600 font-bold transition-all";
                });

                // Show Selected & Highlight Button
                if (tabName === 'ticket') {
                    tabTicket.classList.remove('hidden');
                    btnTicket.className = "flex flex-col items-center text-blue-600 font-bold transition-all";
                } else if (tabName === 'history') {
                    tabHistory.classList.remove('hidden');
                    btnHistory.className = "flex flex-col items-center text-blue-600 font-bold transition-all";
                } else if (tabName === 'profile') {
                    tabProfile.classList.remove('hidden');
                    btnProfile.className = "flex flex-col items-center text-blue-600 font-bold transition-all";
                }
            }

            // Menampilkan Password
            function togglePasswordVisibility(inputId, iconId) {
                const input = document.getElementById(inputId);
                const icon = document.getElementById(iconId);

                if (input.type === 'password') {
                    input.type = 'text';
                    icon.classList.remove('fa-eye');
                    icon.classList.add('fa-eye-slash');
                } else {
                    input.type = 'password';
                    icon.classList.remove('fa-eye-slash');
                    icon.classList.add('fa-eye');
                }
            }

            // 1. Process Login API
            async function handleCustomerLogin(e) {
                e.preventDefault();
                const nopolInput = document.getElementById('login-nopol').value;
                const passwordInput = document.getElementById('login-password').value;

                const nopol = nopolInput.trim().toUpperCase().replace(/\s+/g, '');
                const password = passwordInput.trim();

                const btnLogin = document.getElementById('btn-login');
                const originalText = btnLogin ? btnLogin.innerHTML : '';
                if (btnLogin) {
                    btnLogin.disabled = true;
                    btnLogin.innerHTML = `<i class="fa-solid fa-spinner animate-spin mr-1"></i> <span>Memproses...</span>`;
                }

                try {
                    const response = await fetch('/api/customer/login', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ license_plate: nopol, password: password })
                    });

                    const result = await response.json();

                    if (response.ok && result.status === 'success') {
                        localStorage.setItem(CUSTOMER_KEY, JSON.stringify(result.data));

                        if (result.data.is_first_login) {
                            showFirstChangePasswordScreen(result.data);
                        } else {
                            loadDashboardData(result.data.vehicle_id);
                        }
                    } else {
                        alert(`⚠️ Gagal Login: ${result.message || 'Kredensial tidak valid.'}`);
                    }
                } catch (err) {
                    alert('⚠️ Gagal terhubung ke server backend.');
                } finally {
                    if (btnLogin) {
                        btnLogin.disabled = false;
                        btnLogin.innerHTML = originalText;
                    }
                }
            }

            // 2. Load Dashboard Data API
            async function loadDashboardData(vehicleId) {
                try {
                    const response = await fetch(`/api/customer/ticket-detail?vehicle_id=${vehicleId}`, {
                        headers: { 'Accept': 'application/json' }
                    });
                    const result = await response.json();

                    if (response.ok && result.status === 'success') {
                        showDashboardScreen(result.data);
                    } else {
                        alert(`⚠️ Gagal memuat data: ${result.message || 'Sesi tidak ditemukan.'}`);
                        handleCustomerLogout(false);
                    }
                } catch (err) {
                    console.error("Error load dashboard:", err);
                }
            }

            function showDashboardScreen(data) {
                document.getElementById('section-login').classList.add('hidden');
                document.getElementById('section-change-password-first').classList.add('hidden');
                document.getElementById('section-dashboard').classList.remove('hidden');

                // Tampilkan Bottom Nav & Header Elements
                document.getElementById('customer-bottom-nav').classList.remove('hidden');
                document.getElementById('btn-header-logout').classList.remove('hidden');
                document.getElementById('header-user-info').classList.remove('hidden');

                const vehicle = data.vehicle;
                document.getElementById('header-user-name').innerText = vehicle.driver_name || 'Pengendara';
                document.getElementById('header-nopol-badge').innerText = vehicle.license_plate;
                document.getElementById('header-subtitle').innerText = 'Tiket & Profil Parkir Saya';

                document.getElementById('display-nopol').innerText = vehicle.license_plate;
                document.getElementById('prof-name').value = vehicle.driver_name || '';
                document.getElementById('prof-wa').value = vehicle.whatsapp_number || '';
                document.getElementById('prof-ownership').value = vehicle.ownership_status || 'pribadi';

                // Render QR Code
                const qrContainer = document.getElementById('qrcode');
                qrContainer.innerHTML = '';
                new QRCode(qrContainer, {
                    text: vehicle.qr_token,
                    width: 160,
                    height: 160,
                    colorDark: "#0f172a",
                    colorLight: "#ffffff",
                    correctLevel: QRCode.CorrectLevel.H
                });

                // Render Active Session
                const activeCard = document.getElementById('active-session-card');
                if (data.active_session) {
                    document.getElementById('active-location-name').innerText = data.active_session.location_name;
                    
                    // Tampilkan 3 Bagian Waktu Masuk
                    document.getElementById('active-checkin-day').innerText = data.active_session.check_in_day;
                    document.getElementById('active-checkin-date').innerText = data.active_session.check_in_date;
                    document.getElementById('active-checkin-clock').innerText = data.active_session.check_in_clock;
                    
                    document.getElementById('active-duration').innerText = data.active_session.duration;
                    
                    const estimatedFee = Number(data.active_session.estimated_fee || 0);
                    const feeText = data.active_session.payment_status === 'paid' 
                        ? 'Rp 0 (Lunas)' 
                        : `Rp ${estimatedFee.toLocaleString('id-ID')}`;
                    
                    document.getElementById('active-fee').innerText = feeText;

                    const payBadge = document.getElementById('active-payment-badge');
                    if (data.active_session.payment_status === 'paid') {
                        payBadge.className = "text-[9px] font-bold px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800";
                        payBadge.innerText = "✓ Lunas Awal";
                    } else {
                        payBadge.className = "text-[9px] font-bold px-2 py-0.5 rounded-full bg-amber-200 text-amber-900";
                        payBadge.innerText = "Belum Bayar";
                    }
                    activeCard.classList.remove('hidden');
                } else {
                    activeCard.classList.add('hidden');
                }

                // Render History
                const historyContainer = document.getElementById('history-container');
                historyContainer.innerHTML = '';

                if (data.history && data.history.length > 0) {
                    data.history.forEach(item => {
                        const totalFeeFormatted = Number(item.total_fee || 0).toLocaleString('id-ID');
                        historyContainer.innerHTML += `
                            <div class="bg-slate-50 border border-slate-200 rounded-xl p-3 flex justify-between items-start text-[10px]">
                                <div class="space-y-1">
                                    <h5 class="font-bold text-slate-800 text-xs leading-tight">${item.location_name}</h5>
                                    <div class="text-slate-500 font-medium space-y-0.5">
                                        <p><i class="fa-solid fa-circle-arrow-right text-emerald-500 mr-1"></i> Masuk: <span class="font-bold text-slate-700">${item.check_in_time}</span></p>
                                        <p><i class="fa-solid fa-circle-arrow-left text-rose-500 mr-1"></i> Keluar: <span class="font-bold text-slate-700">${item.check_out_time}</span></p>
                                    </div>
                                </div>
                                <div class="text-right pl-2">
                                    <span class="font-extrabold text-blue-600 text-xs block">Rp ${totalFeeFormatted}</span>
                                    <span class="inline-block mt-1 text-[8px] font-bold px-1.5 py-0.5 rounded bg-slate-200 text-slate-600 uppercase tracking-wider">${item.payment_method}</span>
                                </div>
                            </div>
                        `;
                    });
                } else {
                    historyContainer.innerHTML = `<p class="text-center text-[10px] text-slate-400 py-3">Belum ada riwayat transaksi.</p>`;
                }

                switchCustomerTab('ticket');
            }

            function showFirstChangePasswordScreen(userData) {
                document.getElementById('section-login').classList.add('hidden');
                document.getElementById('section-dashboard').classList.add('hidden');
                document.getElementById('customer-bottom-nav').classList.add('hidden');
                document.getElementById('btn-header-logout').classList.add('hidden');
                document.getElementById('header-user-info').classList.add('hidden');
                document.getElementById('section-change-password-first').classList.remove('hidden');
            }

            // Helper Validasi Nomor HP & Password
            function validateCustomerInput(nopol, waNumber, newPassword = null) {
                // 1. Validasi Angka WA (9 - 13 digit)
                const cleanWA = waNumber.replace(/\D/g, '');
                if (cleanWA.length < 9 || cleanWA.length > 13) {
                    alert('⚠️ Nomor WhatsApp harus berupa angka dengan panjang 9 hingga 13 digit.');
                    return false;
                }

                // 2. Validasi Password tidak Boleh Sama dengan Nopol
                if (newPassword) {
                    const cleanNopol = nopol.trim().toUpperCase().replace(/\s+/g, '');
                    const cleanPass = newPassword.trim().toUpperCase().replace(/\s+/g, '');

                    if (cleanPass === cleanNopol) {
                        alert('⚠️ Demi keamanan, Password/PIN baru tidak boleh sama dengan Nomor Polisi Anda.');
                        return false;
                    }
                }

                return true;
            }

            // 3. Process Ubah Password & Profil Pertama Kali
            async function handleFirstPasswordChange(e) {
                e.preventDefault();
                const session = JSON.parse(localStorage.getItem(CUSTOMER_KEY) || '{}');

                const driverName = document.getElementById('first-driver-name').value;
                const waNumber = document.getElementById('first-wa-number').value;
                const ownershipStatus = document.getElementById('first-ownership').value;
                const newPassword = document.getElementById('first-new-password').value;

                // JALANKAN VALIDASI FRONTEND
                if (!validateCustomerInput(session.license_plate, waNumber, newPassword)) {
                    return;
                }
                try {
                    const response = await fetch('/api/customer/profile/update', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            vehicle_id: session.vehicle_id,
                            driver_name: driverName,
                            whatsapp_number: waNumber,
                            ownership_status: ownershipStatus,
                            new_password: newPassword
                        })
                    });

                    const result = await response.json();

                    if (response.ok && result.status === 'success') {
                        alert('✅ Profil & Password berhasil diperbarui!');
                        session.is_first_login = false;
                        session.driver_name = driverName;
                        session.whatsapp_number = waNumber;
                        session.ownership_status = ownershipStatus;

                        localStorage.setItem(CUSTOMER_KEY, JSON.stringify(session));
                        loadDashboardData(session.vehicle_id);
                    } else {
                        alert(`⚠️ Gagal menyimpan data: ${result.message}`);
                    }
                } catch (err) {
                    alert('⚠️ Terjadi kesalahan koneksi server.');
                }
            }

            // 4. Update Profil Form
            async function handleUpdateProfile(e) {
                e.preventDefault();
                const session = JSON.parse(localStorage.getItem(CUSTOMER_KEY) || '{}');

                const driverName = document.getElementById('prof-name').value;
                const waNumber = document.getElementById('prof-wa').value;
                const ownershipStatus = document.getElementById('prof-ownership').value;

                const payload = {
                    vehicle_id: session.vehicle_id,
                    driver_name: driverName,
                    whatsapp_number: waNumber,
                    ownership_status: ownershipStatus,
                };

                // JALANKAN VALIDASI FRONTEND
                if (!validateCustomerInput(session.license_plate, waNumber)) {
                    return;
                }

                try {
                    const response = await fetch('/api/customer/profile/update', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify(payload)
                    });

                    const result = await response.json();

                    if (response.ok && result.status === 'success') {
                        alert('✅ Profil berhasil diperbarui!');
                        
                        session.driver_name = driverName;
                        session.whatsapp_number = waNumber;
                        session.ownership_status = ownershipStatus;
                        localStorage.setItem(CUSTOMER_KEY, JSON.stringify(session));

                        loadDashboardData(session.vehicle_id);
                    } else {
                        alert(`⚠️ Gagal update: ${result.message}`);
                    }
                } catch (err) {
                    alert('⚠️ Gagal memperbarui profil.');
                }
            }

            // 5. Update Password Opsional dari Tab Profil
            async function handleChangePasswordOptional(e) {
                e.preventDefault();
                const session = JSON.parse(localStorage.getItem(CUSTOMER_KEY) || '{}');
                const newPassword = document.getElementById('opt-new-password').value;
                const waNumber = document.getElementById('prof-wa').value;

                // JALANKAN VALIDASI FRONTEND
                if (!validateCustomerInput(session.license_plate, waNumber, newPassword)) {
                    return;
                }

                try {
                    const response = await fetch('/api/customer/profile/update', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            vehicle_id: session.vehicle_id,
                            driver_name: document.getElementById('prof-name').value || 'Pengendara',
                            whatsapp_number: document.getElementById('prof-wa').value || '-',
                            ownership_status: document.getElementById('prof-ownership').value || 'pribadi',
                            new_password: newPassword
                        })
                    });

                    const result = await response.json();

                    if (response.ok && result.status === 'success') {
                        alert('✅ Password berhasil diperbarui!');
                        document.getElementById('opt-new-password').value = '';
                    } else {
                        alert(`⚠️ Gagal update password: ${result.message}`);
                    }
                } catch (err) {
                    alert('⚠️ Gagal terhubung ke server.');
                }
            }

            function handleCustomerLogout(confirmLogout = true) {
                if (!confirmLogout || confirm('Apakah Anda yakin ingin keluar dari akun ini?')) {
                    localStorage.removeItem(CUSTOMER_KEY);
                    document.getElementById('section-dashboard').classList.add('hidden');
                    document.getElementById('section-change-password-first').classList.add('hidden');
                    document.getElementById('customer-bottom-nav').classList.add('hidden');
                    document.getElementById('btn-header-logout').classList.add('hidden');
                    document.getElementById('header-user-info').classList.add('hidden');
                    
                    document.getElementById('header-subtitle').innerText = 'Portal Pengendara';
                    document.getElementById('section-login').classList.remove('hidden');
                    
                    const loginNopol = document.getElementById('login-nopol');
                    const loginPass = document.getElementById('login-password');
                    if (loginNopol) loginNopol.value = '';
                    if (loginPass) loginPass.value = '';
                }
            }
        </script>
    </x-slot:scripts>

</x-layouts.app>