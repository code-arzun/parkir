<x-layouts.app title="ParkirPWA - POS Penjaga Parkir">

    <!-- SLOT HEADER -->
    <x-slot:header>
        <x-header 
            title="Pos Parkir Utama" 
            :subtitle="'Petugas: ' . (auth()->user()->name ?? 'Petugas')" 
            icon="fa-square-p" 
            iconColor="text-yellow-400"
            :showDashboardLink="true"
            :showLogout="true" 
        />
    </x-slot:header>

    <!-- SLOT NAVIGATION TABS -->
    <x-slot:navigation>
        <div class="flex border-b border-slate-200 bg-white sticky top-0 z-20">
            <button onclick="switchTab('checkin')" id="tab-checkin" class="flex-1 py-3 text-center text-xs font-semibold active-tab flex justify-center items-center space-x-1.5">
                <i class="fa-solid fa-right-to-bracket"></i>
                <span>Check-In Cepat</span>
            </button>
            <button onclick="switchTab('checkout')" id="tab-checkout" class="flex-1 py-3 text-center text-xs font-semibold text-slate-500 flex justify-center items-center space-x-1.5">
                <i class="fa-solid fa-right-from-bracket"></i>
                <span>Check-Out & Aktif</span>
            </button>
        </div>
    </x-slot:navigation>

    <!-- MAIN CONTENT (SLOT UTAMA) -->
    <div class="space-y-4">

        <!-- ================= TAB 1: CHECK-IN FAST FORM ================= -->
        <div id="section-checkin" class="space-y-4">
            
            <div class="grid grid-cols-2 gap-3">
                <button type="button" onclick="setVehicleType('motor')" id="btn-type-motor" 
                    class="p-3.5 rounded-xl border-2 border-blue-600 bg-blue-50 text-blue-700 font-bold text-xs flex items-center justify-center space-x-2 transition-all">
                    <i class="fa-solid fa-motorcycle text-xl"></i>
                    <span>Motor (Roda 2)</span>
                </button>
                <button type="button" onclick="setVehicleType('sepeda')" id="btn-type-sepeda" 
                    class="p-3.5 rounded-xl border-2 border-slate-200 bg-slate-50 text-slate-600 font-bold text-xs flex items-center justify-center space-x-2 transition-all">
                    <i class="fa-solid fa-bicycle text-xl"></i>
                    <span>Sepeda Ontel</span>
                </button>
            </div>

            <form id="form-checkin" onsubmit="handleCheckIn(event)" class="space-y-4">
                
               <div id="field-nopol" class="relative">
                    <div class="flex justify-between items-center mb-1">
                        <label class="block text-[11px] font-bold text-slate-700 uppercase">Nomor Polisi (Plat Nomor)</label>
                        <!-- Status Badge Realtime -->
                        <span id="nopol-status-badge" class="hidden text-[10px] font-bold px-2 py-0.5 rounded-full"></span>
                    </div>

                    <input type="text" id="input-nopol" autocomplete="off" placeholder="Ketik Nopol (contoh: B1234XYZ)" required
                        oninput="handleNopolInput(this)"
                        class="w-full px-4 py-3.5 bg-slate-50 border border-slate-300 rounded-2xl text-lg font-black uppercase tracking-wider focus:bg-white focus:ring-2 focus:ring-blue-600 focus:outline-none">

                    <!-- DROPDOWN SUGGESTION LIST -->
                    <div id="nopol-suggestions" class="hidden absolute left-0 right-0 top-full mt-1 bg-white border border-slate-200 rounded-2xl shadow-xl z-30 overflow-hidden divide-y divide-slate-100">
                    </div>
                </div>

                <!-- FOTO MASTER OPSIONAL -->
                <div>
                    <div class="border-2 border-dashed border-slate-300 rounded-2xl p-3 bg-slate-50 text-center">
                        <video id="camera-preview" class="hidden w-full h-36 object-cover rounded-xl mb-2 shadow-sm" autoplay playsinline></video>
                        <img id="photo-result" class="hidden w-full h-36 object-cover rounded-xl mb-2 shadow-sm" alt="Foto Master">

                        <div id="camera-buttons" class="flex justify-center space-x-2">
                            <button type="button" onclick="startCamera()" id="btn-start-cam" class="bg-slate-800 text-white text-xs px-3 py-2 rounded-xl font-bold flex items-center space-x-1.5">
                                <i class="fa-solid fa-camera"></i>
                                <span>Ambil Foto Master (Opsional)</span>
                            </button>
                            <button type="button" onclick="capturePhoto()" id="btn-capture" class="hidden bg-emerald-600 text-white text-xs px-3 py-2 rounded-xl font-bold">
                                <span>Jepret Foto</span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- TOGGLE BAYAR AWAL -->
                <div class="bg-amber-50 border border-amber-200 rounded-2xl p-3 flex items-center justify-between">
                    <div>
                        <span class="text-xs font-bold text-amber-900 block">Bayar Lunas di Awal?</span>
                        <span class="text-[10px] text-amber-700">Centang jika bayar langsung saat masuk</span>
                    </div>
                    <input type="checkbox" id="input-pay-upfront" class="w-5 h-5 accent-blue-600 rounded">
                </div>

                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-black py-4 rounded-2xl shadow-lg shadow-blue-600/30 transition-all text-xs flex items-center justify-center space-x-2 tracking-wide">
                    <i class="fa-solid fa-bolt text-sm"></i>
                    <span>CHECK-IN SEKARANG</span>
                </button>
            </form>
        </div>


        <!-- ================= TAB 2: CHECK-OUT & DAFTAR AKTIF ================= -->
        <div id="section-checkout" class="hidden space-y-5">
            
            <!-- SCANNER KAMERA -->
            <div class="bg-slate-900 rounded-2xl p-3 text-center text-white shadow-md">
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block mb-2">Scan QR Code Tiket Pelanggan</span>
                <div id="reader" class="w-full min-h-[200px] overflow-hidden rounded-xl bg-black flex items-center justify-center"></div>
                <button onclick="restartScanner()" class="mt-2.5 text-[10px] bg-slate-800 text-blue-400 font-bold px-3 py-1 rounded-lg border border-slate-700">
                    <i class="fa-solid fa-arrows-rotate mr-1"></i> Restart Scanner
                </button>
            </div>

            <!-- CARI MANUAL / LIST AKTIF -->
            <div>
                <div class="flex justify-between items-center mb-2">
                    <h3 class="text-xs font-bold text-slate-700 uppercase tracking-wider">Kendaraan Terparkir Saat Ini</h3>
                    <span class="bg-blue-100 text-blue-700 text-[10px] font-black px-2 py-0.5 rounded-full" id="active-count">0 Kendaraan</span>
                </div>

                <div class="relative mb-3">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                        <i class="fa-solid fa-magnifying-glass text-xs"></i>
                    </span>
                    <input type="text" id="search-checkout" onkeyup="filterActiveList()" placeholder="Cari Nopol di daftar..."
                        class="w-full pl-9 pr-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs font-bold uppercase focus:bg-white focus:outline-none">
                </div>

                <!-- DAFTAR CARDS KENDARAAN AKTIF DARI DB -->
                <div id="active-list-container" class="space-y-2">
                    <p class="text-center text-xs text-slate-400 py-4">Memuat data dari database...</p>
                </div>
            </div>

            <!-- MODAL CHECKOUT RESULT -->
            <div id="checkout-result" class="hidden bg-white border-2 border-blue-600 rounded-2xl p-4 shadow-2xl space-y-3">
                <div class="flex justify-between items-center border-b border-slate-100 pb-2">
                    <div>
                        <span class="text-[10px] font-bold text-slate-400 uppercase">Proses Check-Out</span>
                        <h3 class="text-xl font-black text-slate-800" id="res-nopol">-</h3>
                    </div>
                    <button onclick="closeCheckoutResult()" class="text-slate-400 hover:text-slate-600 text-sm font-bold">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>

                <div class="bg-slate-50 rounded-xl p-3 grid grid-cols-2 gap-2 text-center border border-slate-100">
                    <div>
                        <span class="text-[10px] text-slate-400 block">Durasi Parkir</span>
                        <span class="text-xs font-bold text-slate-700" id="res-duration">-</span>
                    </div>
                    <div>
                        <span class="text-[10px] text-slate-400 block">Total Tagihan</span>
                        <span class="text-base font-extrabold text-blue-600" id="res-fee">Rp 0</span>
                    </div>
                </div>

                <div>
                    <label class="block text-[10px] font-bold text-slate-600 uppercase mb-1">Metode Pembayaran</label>
                    <div class="grid grid-cols-2 gap-2">
                        <label class="border rounded-lg p-2 flex items-center space-x-2 text-xs font-bold cursor-pointer hover:bg-slate-50">
                            <input type="radio" name="payment_method" value="cash" checked class="accent-blue-600">
                            <span>Tunai (Cash)</span>
                        </label>
                        <label class="border rounded-lg p-2 flex items-center space-x-2 text-xs font-bold cursor-pointer hover:bg-slate-50">
                            <input type="radio" name="payment_method" value="qris" class="accent-blue-600">
                            <span>QRIS Digital</span>
                        </label>
                    </div>
                </div>

                <button id="btn-confirm-checkout" onclick="processCheckoutFinal()" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3 rounded-xl shadow-lg shadow-emerald-600/30 text-xs flex items-center justify-center space-x-2">
                    <i class="fa-solid fa-circle-check"></i>
                    <span>LUNAS & SELESAIKAN PARKIR</span>
                </button>
            </div>

        </div>

    </div>

    <!-- SLOT SCRIPTS JAVASCRIPT LOGIC -->
    <x-slot:scripts>
        <script>
            let currentVehicleType = 'motor';
            let html5QrCodeInstance = null;
            let videoStream = null;
            let selectedSessionId = null;
            let searchTimeout = null;

            // 0. Real-time Search & Autocomplete Nopol
            function handleNopolInput(input) {
                const rawVal = input.value.trim().toUpperCase().replace(/\s+/g, '');
                const badge = document.getElementById('nopol-status-badge');
                const suggestionBox = document.getElementById('nopol-suggestions');

                if (!badge || !suggestionBox) return;

                // Reset jika karakter kurang dari 2
                if (rawVal.length < 2) {
                    badge.classList.add('hidden');
                    suggestionBox.classList.add('hidden');
                    suggestionBox.innerHTML = '';
                    return;
                }

                // Debounce request agar server tidak terlalu sibuk
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(async () => {
                    try {
                        const response = await fetch(`/api/parking/vehicles/search?query=${encodeURIComponent(rawVal)}`, {
                            headers: { 'Accept': 'application/json' }
                        });
                        const result = await response.json();

                        if (response.ok && result.status === 'success') {
                            const matches = result.data || [];
                            const exactMatch = matches.find(v => v.license_plate.toUpperCase().replace(/\s+/g, '') === rawVal);

                            // Tampilkan Badge Status Pengendara
                            if (exactMatch) {
                                badge.className = "text-[10px] font-bold px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 border border-emerald-200";
                                badge.innerText = "✓ Pengendara Terdaftar";
                            } else {
                                badge.className = "text-[10px] font-bold px-2 py-0.5 rounded-full bg-blue-100 text-blue-800 border border-blue-200";
                                badge.innerText = "➕ Pengendara Baru";
                            }
                            badge.classList.remove('hidden');

                            // Tampilkan Dropdown Rekomendasi Nopol
                            if (matches.length > 0) {
                                suggestionBox.innerHTML = '';
                                matches.forEach(item => {
                                    const driverText = item.driver_name ? ` (${item.driver_name})` : '';
                                    suggestionBox.innerHTML += `
                                        <div onclick="selectNopolSuggestion('${item.license_plate}')" 
                                            class="p-3 hover:bg-blue-50 cursor-pointer flex justify-between items-center text-xs font-bold text-slate-800 transition-all">
                                            <div class="flex items-center space-x-2">
                                                <i class="fa-solid fa-clock-rotate-left text-slate-400"></i>
                                                <span>${item.license_plate} <span class="text-slate-400 font-normal">${driverText}</span></span>
                                            </div>
                                            <span class="text-[9px] bg-slate-100 text-slate-600 px-2 py-0.5 rounded-md uppercase">Pilih</span>
                                        </div>
                                    `;
                                });
                                suggestionBox.classList.remove('hidden');
                            } else {
                                suggestionBox.classList.add('hidden');
                            }
                        }
                    } catch (err) {
                        console.error("Gagal melakukan pencarian nopol:", err);
                    }
                }, 250);
            }

            function selectNopolSuggestion(nopol) {
                const input = document.getElementById('input-nopol');
                if (input) {
                    input.value = nopol;
                    const suggestionBox = document.getElementById('nopol-suggestions');
                    if (suggestionBox) suggestionBox.classList.add('hidden');
                    handleNopolInput(input);
                }
            }

            // Sembunyikan saran nopol saat klik di luar area input
            document.addEventListener('click', (e) => {
                const fieldNopol = document.getElementById('field-nopol');
                if (fieldNopol && !fieldNopol.contains(e.target)) {
                    const suggestionBox = document.getElementById('nopol-suggestions');
                    if (suggestionBox) suggestionBox.classList.add('hidden');
                }
            });

            // 1. Camera Logic
            function stopCheckInCamera() {
                const video = document.getElementById('camera-preview');
                if (video) {
                    video.pause();
                    video.srcObject = null;
                    video.classList.add('hidden');
                }
                if (videoStream) {
                    videoStream.getTracks().forEach(track => track.stop());
                    videoStream = null;
                }
                const btnStart = document.getElementById('btn-start-cam');
                const btnCapture = document.getElementById('btn-capture');
                if (btnStart) btnStart.classList.remove('hidden');
                if (btnCapture) btnCapture.classList.add('hidden');
            }

            async function startCamera() {
                await stopScanner();
                try {
                    videoStream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' } });
                    const video = document.getElementById('camera-preview');
                    video.srcObject = videoStream;
                    video.play();
                    video.classList.remove('hidden');
                    document.getElementById('photo-result').classList.add('hidden');
                    document.getElementById('btn-start-cam').classList.add('hidden');
                    document.getElementById('btn-capture').classList.remove('hidden');
                } catch (err) {
                    alert('Gagal mengakses kamera HP: ' + err.message);
                }
            }

            function capturePhoto() {
                const video = document.getElementById('camera-preview');
                const canvas = document.createElement('canvas');
                canvas.width = video.videoWidth;
                canvas.height = video.videoHeight;
                canvas.getContext('2d').drawImage(video, 0, 0);

                const imgUrl = canvas.toDataURL('image/jpeg');
                const imgResult = document.getElementById('photo-result');
                imgResult.src = imgUrl;
                imgResult.classList.remove('hidden');

                stopCheckInCamera();
                const btnStart = document.getElementById('btn-start-cam');
                if (btnStart) {
                    btnStart.classList.remove('hidden');
                    btnStart.innerText = "Foto Ulang";
                }
            }

            // 2. Switch Tab Navigation
            function switchTab(tab) {
                document.getElementById('section-checkin').classList.add('hidden');
                document.getElementById('section-checkout').classList.add('hidden');
                document.getElementById('tab-checkin').classList.remove('active-tab', 'text-blue-600');
                document.getElementById('tab-checkout').classList.remove('active-tab', 'text-blue-600');

                if (tab === 'checkin') {
                    document.getElementById('section-checkin').classList.remove('hidden');
                    document.getElementById('tab-checkin').classList.add('active-tab');
                    stopScanner();
                } else {
                    document.getElementById('section-checkout').classList.remove('hidden');
                    document.getElementById('tab-checkout').classList.add('active-tab');
                    stopCheckInCamera();
                    loadActiveParkingList();
                    setTimeout(() => { initScanner(); }, 300);
                }
            }

            // 3. Set Vehicle Type
            function setVehicleType(type) {
                currentVehicleType = type;
                const btnMotor = document.getElementById('btn-type-motor');
                const btnSepeda = document.getElementById('btn-type-sepeda');
                const fieldNopol = document.getElementById('field-nopol');

                if (type === 'sepeda') {
                    btnSepeda.className = "p-3.5 rounded-xl border-2 border-blue-600 bg-blue-50 text-blue-700 font-bold text-xs flex items-center justify-center space-x-2 transition-all";
                    btnMotor.className = "p-3.5 rounded-xl border-2 border-slate-200 bg-slate-50 text-slate-600 font-bold text-xs flex items-center justify-center space-x-2 transition-all";
                    fieldNopol.classList.add('hidden');
                    document.getElementById('input-nopol').removeAttribute('required');
                } else {
                    btnMotor.className = "p-3.5 rounded-xl border-2 border-blue-600 bg-blue-50 text-blue-700 font-bold text-xs flex items-center justify-center space-x-2 transition-all";
                    btnSepeda.className = "p-3.5 rounded-xl border-2 border-slate-200 bg-slate-50 text-slate-600 font-bold text-xs flex items-center justify-center space-x-2 transition-all";
                    fieldNopol.classList.remove('hidden');
                    document.getElementById('input-nopol').setAttribute('required', 'true');
                }
            }

            // 4. AJAX POST Check-In
            async function handleCheckIn(e) {
                e.preventDefault();

                const submitBtn = e.target.querySelector('button[type="submit"]');
                const originalText = submitBtn.innerHTML;
                
                submitBtn.disabled = true;
                submitBtn.innerHTML = `<i class="fa-solid fa-spinner animate-spin mr-1"></i> <span>Menyimpan...</span>`;

                try {
                    const formData = new FormData();
                    formData.append('vehicle_type', currentVehicleType);
                    
                    if (currentVehicleType === 'motor') {
                        formData.append('license_plate', document.getElementById('input-nopol').value);
                    }
                    
                    formData.append('pay_upfront', document.getElementById('input-pay-upfront').checked ? 1 : 0);

                    const photoResult = document.getElementById('photo-result');
                    if (!photoResult.classList.contains('hidden') && photoResult.src) {
                        const blob = await (await fetch(photoResult.src)).blob();
                        formData.append('master_photo', blob, 'master.jpg');
                    }

                    const response = await fetch('/api/parking/check-in', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: formData
                    });

                    const result = await response.json();

                    if (response.ok && result.status === 'success') {
                        alert(`✅ CHECK-IN BERHASIL!\n\nNopol/ID: ${result.data.license_plate}\nWaktu: ${result.data.check_in_time}`);
                        
                        stopCheckInCamera();
                        document.getElementById('form-checkin').reset();
                        document.getElementById('photo-result').classList.add('hidden');
                        
                        // Reset badge & suggestions
                        const badge = document.getElementById('nopol-status-badge');
                        if (badge) badge.classList.add('hidden');
                        
                        setVehicleType('motor');
                    } else {
                        alert(`⚠️ Gagal Simpan: ${result.message || 'Terjadi kesalahan pada sistem.'}`);
                    }

                } catch (err) {
                    console.error("Error Check-In:", err);
                    alert("⚠️ Gagal terhubung ke server backend.");
                } finally {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;
                }
            }

            // 5. Fetch Active Parking List
            async function loadActiveParkingList() {
                const container = document.getElementById('active-list-container');
                const countBadge = document.getElementById('active-count');

                try {
                    const response = await fetch('/api/parking/sessions?status=active', {
                        headers: { 'Accept': 'application/json' }
                    });
                    const result = await response.json();

                    if (response.ok && result.status === 'success') {
                        const sessions = result.data.data || result.data;
                        countBadge.innerText = `${sessions.length} Kendaraan`;
                        container.innerHTML = '';

                        if (sessions.length === 0) {
                            container.innerHTML = `<p class="text-center text-xs text-slate-400 py-4">Belum ada kendaraan terparkir.</p>`;
                            return;
                        }

                        sessions.forEach(item => {
                            const icon = item.vehicle.vehicle_type === 'motor' ? 'fa-motorcycle' : 'fa-bicycle';
                            const colorClass = item.vehicle.vehicle_type === 'motor' ? 'text-blue-600 bg-blue-50' : 'text-emerald-600 bg-emerald-50';
                            
                            container.innerHTML += `
                                <div class="bg-white border border-slate-200 rounded-xl p-3 shadow-sm flex justify-between items-center hover:border-blue-400 cursor-pointer" 
                                    onclick="fetchCheckoutPreview('${item.vehicle.license_plate}')">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-10 h-10 ${colorClass} rounded-lg flex items-center justify-center font-bold text-sm">
                                            <i class="fa-solid ${icon}"></i>
                                        </div>
                                        <div>
                                            <h4 class="text-sm font-black text-slate-800 leading-tight">${item.vehicle.license_plate}</h4>
                                            <p class="text-[10px] text-slate-400">Masuk: ${item.check_in_time}</p>
                                        </div>
                                    </div>
                                    <button class="bg-slate-900 hover:bg-blue-600 text-white font-bold text-[10px] px-3 py-2 rounded-lg transition-all">
                                        Keluar &rarr;
                                    </button>
                                </div>
                            `;
                        });
                    }
                } catch (err) {
                    console.error("Gagal load sesi aktif:", err);
                    container.innerHTML = `<p class="text-center text-xs text-red-400 py-4">Gagal memuat daftar kendaraan.</p>`;
                }
            }

            // 6. QR Code Scanner Core Engine
            async function initScanner() {
                await stopScanner();
                const readerElement = document.getElementById("reader");
                if (!readerElement) return;

                html5QrCodeInstance = new Html5Qrcode("reader");
                const config = { fps: 10, qrbox: { width: 200, height: 200 } };

                html5QrCodeInstance.start({ facingMode: "environment" }, config, onScanSuccess, onScanFailure)
                    .catch(err => {
                        html5QrCodeInstance.start({ facingMode: "user" }, config, onScanSuccess, onScanFailure);
                    });
            }

            async function stopScanner() {
                if (html5QrCodeInstance) {
                    try {
                        if (html5QrCodeInstance.isScanning) {
                            await html5QrCodeInstance.stop();
                        }
                        html5QrCodeInstance.clear();
                    } catch (err) {}
                    html5QrCodeInstance = null;
                }
            }

            function restartScanner() {
                stopScanner().then(() => { setTimeout(() => { initScanner(); }, 200); });
            }

            function onScanSuccess(decodedText) {
                fetchCheckoutPreview(decodedText);
            }

            function onScanFailure(error) {}

            // 7. Preview & Process Check-Out
            async function fetchCheckoutPreview(searchQuery) {
                try {
                    const response = await fetch(`/api/parking/check-out/preview?search=${encodeURIComponent(searchQuery)}`, {
                        headers: { 'Accept': 'application/json' }
                    });
                    const result = await response.json();

                    if (response.ok && result.status === 'success') {
                        const data = result.data;
                        selectedSessionId = data.session_id;

                        document.getElementById('res-nopol').innerText = data.license_plate;
                        document.getElementById('res-duration').innerText = data.duration.text;
                        document.getElementById('res-fee').innerText = `Rp ${data.remaining_fee.toLocaleString('id-ID')}`;

                        document.getElementById('checkout-result').classList.remove('hidden');
                    } else {
                        alert(result.message || 'Data parkir tidak ditemukan.');
                    }
                } catch (err) {
                    alert('Gagal mengambil data preview check-out.');
                }
            }

            async function processCheckoutFinal() {
                if (!selectedSessionId) return;

                const paymentMethod = document.querySelector('input[name="payment_method"]:checked').value;
                const btnConfirm = document.getElementById('btn-confirm-checkout');
                btnConfirm.disabled = true;

                try {
                    const response = await fetch(`/api/parking/check-out/${selectedSessionId}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ payment_method: paymentMethod })
                    });

                    const result = await response.json();

                    if (response.ok && result.status === 'success') {
                        alert('✅ Pembayaran Lunas & Transaksi Selesai!');
                        closeCheckoutResult();
                        loadActiveParkingList();
                    } else {
                        alert(`⚠️ Gagal Check-Out: ${result.message}`);
                    }
                } catch (err) {
                    alert('Gagal memproses transaksi check-out.');
                } finally {
                    btnConfirm.disabled = false;
                }
            }

            function closeCheckoutResult() {
                selectedSessionId = null;
                document.getElementById('checkout-result').classList.add('hidden');
            }

            function filterActiveList() {
                const query = document.getElementById('search-checkout').value.toLowerCase();
                const items = document.querySelectorAll('#active-list-container > div');
                items.forEach(item => {
                    const text = item.innerText.toLowerCase();
                    item.style.display = text.includes(query) ? 'flex' : 'none';
                });
            }
        </script>
    </x-slot:scripts>

</x-layouts.app>