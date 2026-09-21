<x-layouts.app title="ParkirPWA - Pengaturan Tarif">

    <!-- SLOT HEADER -->
    <x-slot:header>
        <x-header 
            title="Pengaturan Tarif" 
            :subtitle="auth()->user()->name ?? 'Owner'" 
            icon="fa-sliders" 
            iconColor="text-amber-400"
            :showLogout="true"
        />
    </x-slot:header>

    <!-- MAIN CONTENT AREA -->
    <div class="space-y-4">
        
        <!-- SELECTOR JENIS KENDARAAN -->
        <div class="flex border border-slate-200 rounded-xl overflow-hidden p-1 bg-slate-100">
            <button type="button" onclick="switchRateForm('motor')" id="btn-rate-motor" class="flex-1 py-2 text-xs font-bold rounded-lg bg-white text-blue-600 shadow-sm transition-all">
                <i class="fa-solid fa-motorcycle mr-1"></i> Motor
            </button>
            <button type="button" onclick="switchRateForm('sepeda')" id="btn-rate-sepeda" class="flex-1 py-2 text-xs font-bold rounded-lg text-slate-500 transition-all">
                <i class="fa-solid fa-bicycle mr-1"></i> Sepeda
            </button>
        </div>

        <!-- FORM ATURAN TARIF -->
        <form id="form-rate" onsubmit="saveRateSetting(event)" class="space-y-3 bg-white border border-slate-200 rounded-2xl p-4 shadow-sm">
            <input type="hidden" id="rate-vehicle-type" value="motor">

            <div>
                <label class="block text-[10px] font-bold text-slate-600 uppercase mb-1">Model Skema Tarif</label>
                <select id="rate-type" onchange="handleRateTypeChange()" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs font-bold focus:bg-white focus:outline-none">
                    <option value="progressive">Progresif (Berhitung per Jam)</option>
                    <option value="flat">Flat (Sekali Masuk Bayar Tetap)</option>
                </select>
            </div>

            <div class="grid grid-cols-2 gap-2">
                <div>
                    <label class="block text-[10px] font-bold text-slate-600 uppercase mb-1">Tarif Jam 1 (Rp)</label>
                    <input type="text" inputmode="numeric" id="base-rate" 
                        value="{{ isset($motorRate->base_rate) ? number_format($motorRate->base_rate, 0, ',', '.') : '2.000' }}" required
                        oninput="formatRupiah(this)"
                        class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs font-bold focus:bg-white focus:outline-none">
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-slate-600 uppercase mb-1">Jam Berikutnya (Rp)</label>
                    <input type="text" inputmode="numeric" id="hourly-rate" 
                        value="{{ isset($motorRate->hourly_rate) ? number_format($motorRate->hourly_rate, 0, ',', '.') : '1.000' }}" required
                        oninput="formatRupiah(this)"
                        class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs font-bold focus:bg-white focus:outline-none">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-2">
                <div>
                    <label class="block text-[10px] font-bold text-slate-600 uppercase mb-1">Tenggang Gratis (Mnt)</label>
                    <input type="text" inputmode="numeric" id="grace-period" 
                        value="{{ $motorRate->grace_period_minutes ?? 10 }}" required
                        oninput="formatRupiah(this)"
                        class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs font-bold focus:bg-white focus:outline-none">
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-slate-600 uppercase mb-1">Max Per Hari (Rp)</label>
                    <input type="text" inputmode="numeric" id="max-daily-rate" 
                        value="{{ isset($motorRate->max_daily_rate) ? number_format($motorRate->max_daily_rate, 0, ',', '.') : '' }}" placeholder="Opsional"
                        oninput="formatRupiah(this)"
                        class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs font-bold focus:bg-white focus:outline-none">
                </div>
            </div>

            <div>
                <label class="block text-[10px] font-bold text-slate-600 uppercase mb-1">Denda Tiket Hilang (Rp)</label>
                <input type="text" inputmode="numeric" id="lost-ticket-penalty" 
                    value="{{ isset($motorRate->lost_ticket_penalty) ? number_format($motorRate->lost_ticket_penalty, 0, ',', '.') : '20.000' }}" required
                    oninput="formatRupiah(this)"
                    class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs font-bold focus:bg-white focus:outline-none">
            </div>

            <button type="submit" class="w-full mt-2 bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded-xl text-xs shadow-md shadow-blue-600/30 flex justify-center items-center space-x-1.5">
                <i class="fa-solid fa-floppy-disk"></i>
                <span>SIMPAN ATURAN TARIF</span>
            </button>
        </form>

    </div>

    <!-- SCRIPTS LOGIC -->
    <x-slot:scripts>
        <script>
    // Helper Format Ribuan
    function formatNumber(val) {
        if (!val) return '';
        const num = parseInt(val.toString().replace(/\D/g, ''), 10);
        return isNaN(num) ? '' : num.toLocaleString('id-ID');
    }

    function cleanNumber(val) {
        if (!val) return 0;
        return val.toString().replace(/\./g, '').replace(/,/g, '');
    }

    function formatRupiah(element) {
        element.value = formatNumber(element.value);
    }

    // PEMBARUAN 1: Fungsi untuk toggle disable/enable input Jam Berikutnya saat skema tarif berubah
    function handleRateTypeChange() {
        const rateType = document.getElementById('rate-type').value;
        const hourlyInput = document.getElementById('hourly-rate');

        if (rateType === 'flat') {
            hourlyInput.value = '0';
            hourlyInput.disabled = true;
            hourlyInput.classList.add('bg-slate-200', 'cursor-not-allowed', 'opacity-60');
            hourlyInput.classList.remove('bg-slate-50', 'focus:bg-white');
        } else {
            hourlyInput.disabled = false;
            hourlyInput.classList.remove('bg-slate-200', 'cursor-not-allowed', 'opacity-60');
            hourlyInput.classList.add('bg-slate-50', 'focus:bg-white');
        }
    }

    const rateData = {
        motor: {
            type: "{{ $motorRate->rate_type ?? 'progressive' }}",
            base: formatNumber("{{ isset($motorRate->base_rate) ? (int)$motorRate->base_rate : 2000 }}"),
            hourly: formatNumber("{{ isset($motorRate->hourly_rate) ? (int)$motorRate->hourly_rate : 1000 }}"),
            grace: formatNumber("{{ $motorRate->grace_period_minutes ?? 10 }}"),
            max: formatNumber("{{ isset($motorRate->max_daily_rate) ? (int)$motorRate->max_daily_rate : '' }}"),
            lost: formatNumber("{{ isset($motorRate->lost_ticket_penalty) ? (int)$motorRate->lost_ticket_penalty : 20000 }}")
        },
        sepeda: {
            type: "{{ $sepedaRate->rate_type ?? 'flat' }}",
            base: formatNumber("{{ isset($sepedaRate->base_rate) ? (int)$sepedaRate->base_rate : 1000 }}"),
            hourly: formatNumber("{{ isset($sepedaRate->hourly_rate) ? (int)$sepedaRate->hourly_rate : 0 }}"),
            grace: formatNumber("{{ $sepedaRate->grace_period_minutes ?? 10 }}"),
            max: formatNumber("{{ isset($sepedaRate->max_daily_rate) ? (int)$sepedaRate->max_daily_rate : '' }}"),
            lost: formatNumber("{{ isset($sepedaRate->lost_ticket_penalty) ? (int)$sepedaRate->lost_ticket_penalty : 10000 }}")
        }
    };

    function switchRateForm(type) {
        document.getElementById('rate-vehicle-type').value = type;
        const btnMotor = document.getElementById('btn-rate-motor');
        const btnSepeda = document.getElementById('btn-rate-sepeda');

        if (type === 'motor') {
            btnMotor.className = "flex-1 py-2 text-xs font-bold rounded-lg bg-white text-blue-600 shadow-sm transition-all";
            btnSepeda.className = "flex-1 py-2 text-xs font-bold rounded-lg text-slate-500 transition-all";
        } else {
            btnSepeda.className = "flex-1 py-2 text-xs font-bold rounded-lg bg-white text-blue-600 shadow-sm transition-all";
            btnMotor.className = "flex-1 py-2 text-xs font-bold rounded-lg text-slate-500 transition-all";
        }

        const data = rateData[type];
        document.getElementById('rate-type').value = data.type;
        document.getElementById('base-rate').value = data.base;
        document.getElementById('hourly-rate').value = data.hourly;
        document.getElementById('grace-period').value = data.grace;
        document.getElementById('max-daily-rate').value = data.max;
        document.getElementById('lost-ticket-penalty').value = data.lost;

        // PEMBARUAN 2: Jalankan penyesuaian tampilan input jam berikutnya
        handleRateTypeChange();
    }

    // PEMBARUAN 3: Daftarkan event listener saat halaman selesai dimuat & select box diubah
    document.addEventListener('DOMContentLoaded', () => {
        const rateTypeSelect = document.getElementById('rate-type');
        if (rateTypeSelect) {
            rateTypeSelect.addEventListener('change', handleRateTypeChange);
        }
        // Run pertama kali saat load
        handleRateTypeChange();
    });

    async function saveRateSetting(e) {
        e.preventDefault();
        const vehicleType = document.getElementById('rate-vehicle-type').value;

        // Bersihkan titik separator sebelum dikirim ke Laravel API
        const payload = {
            vehicle_type: vehicleType,
            rate_type: document.getElementById('rate-type').value,
            base_rate: cleanNumber(document.getElementById('base-rate').value),
            hourly_rate: cleanNumber(document.getElementById('hourly-rate').value),
            grace_period_minutes: cleanNumber(document.getElementById('grace-period').value),
            max_daily_rate: cleanNumber(document.getElementById('max-daily-rate').value) || null,
            lost_ticket_penalty: cleanNumber(document.getElementById('lost-ticket-penalty').value),
        };

        try {
            const response = await fetch('/api/parking/rates', {
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
                alert(`✅ Aturan Tarif ${vehicleType.toUpperCase()} Berhasil Disimpan!`);
                
                rateData[vehicleType] = {
                    type: payload.rate_type,
                    base: formatNumber(payload.base_rate),
                    hourly: formatNumber(payload.hourly_rate),
                    grace: formatNumber(payload.grace_period_minutes),
                    max: formatNumber(payload.max_daily_rate),
                    lost: formatNumber(payload.lost_ticket_penalty)
                };
            } else {
                alert(`⚠️ Gagal menyimpan: ${result.message || 'Periksa inputan Anda.'}`);
            }
        } catch (err) {
            alert('⚠️ Terjadi kesalahan saat menghubungkan ke server.');
        }
    }
</script>
    </x-slot:scripts>

</x-layouts.app>