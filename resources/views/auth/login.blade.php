<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Petugas - ParkirPWA</title>

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background-color: #0f172a;
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4">

    <div class="w-full max-w-md bg-slate-900 border border-slate-800 rounded-3xl p-8 shadow-2xl relative overflow-hidden">
        
        <!-- Decorative Background Effect -->
        <div class="absolute -top-10 -right-10 w-32 h-32 bg-blue-600/20 rounded-full blur-2xl"></div>
        <div class="absolute -bottom-10 -left-10 w-32 h-32 bg-indigo-600/20 rounded-full blur-2xl"></div>

        <!-- HEADER -->
        <div class="text-center mb-8 relative z-10">
            <div class="w-16 h-16 bg-blue-600/10 border border-blue-500/20 rounded-2xl flex items-center justify-center mx-auto mb-4 text-blue-500 shadow-inner">
                <i class="fa-solid fa-square-p text-3xl"></i>
            </div>
            <h1 class="text-2xl font-black text-white tracking-tight">ParkirPWA</h1>
            <p class="text-xs text-slate-400 mt-1">Portal Masuk Penjaga & Pemilik Parkir</p>
        </div>

        <!-- ALERT ERROR (JIKA GAGAL LOGIN) -->
        @if ($errors->any())
            <div class="mb-6 p-4 rounded-2xl bg-red-500/10 border border-red-500/20 text-red-400 text-xs flex items-center space-x-3">
                <i class="fa-solid fa-triangle-exclamation text-base"></i>
                <div>
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- FORM LOGIN -->
        <form action="{{ route('login') }}" method="POST" class="space-y-5 relative z-10">
            @csrf

            <!-- INPUT EMAIL -->
            <div>
                <label class="block text-[11px] font-bold text-slate-300 uppercase mb-2">Email Petugas / Owner</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-slate-500">
                        <i class="fa-solid fa-envelope text-sm"></i>
                    </span>
                    <input type="email" name="email" value="{{ old('email') }}" required autofocus placeholder="petugas@parkir.com"
                        class="w-full pl-11 pr-4 py-3.5 bg-slate-800/60 border border-slate-700/80 rounded-2xl text-xs text-white placeholder-slate-500 focus:bg-slate-800 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 focus:outline-none transition-all">
                </div>
            </div>

            <!-- INPUT PASSWORD -->
            <div>
                <label class="block text-[11px] font-bold text-slate-300 uppercase mb-2">Kata Sandi</label>
                <div class="relative">
                    <input type="password" name="password" id="password" required placeholder="Password"
                        class="w-full pl-4 pr-10 py-3 bg-slate-50 border border-slate-300 rounded-xl text-xs font-bold focus:bg-white focus:ring-2 focus:ring-blue-600 focus:outline-none">
                    <button type="button" onclick="togglePasswordVisibility('password', 'icon-login-eye')" 
                        class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 p-1 text-xs">
                        <i id="icon-login-eye" class="fa-solid fa-eye"></i>
                    </button>
                </div>
            </div>

            <!-- REMEMBER ME -->
            <div class="flex items-center justify-between text-xs text-slate-400">
                <label class="flex items-center space-x-2 cursor-pointer">
                    <input type="checkbox" name="remember" class="w-4 h-4 bg-slate-800 border-slate-700 rounded accent-blue-600">
                    <span>Ingat Saya</span>
                </label>
            </div>

            <!-- SUBMIT BUTTON -->
            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-500 text-white font-bold py-3.5 rounded-2xl shadow-lg shadow-blue-600/30 transition-all text-xs flex items-center justify-center space-x-2">
                <span>MASUK KE SISTEM</span>
                <i class="fa-solid fa-arrow-right-to-bracket text-xs"></i>
            </button>
        </form>

        <!-- FOOTER -->
        <div class="mt-8 text-center border-t border-slate-800/60 pt-6">
            <p class="text-[11px] text-slate-500">Akses pelanggan tanpa login akun petugas?</p>
            <a href="{{ route('pengguna.app') }}" class="text-xs font-bold text-blue-400 hover:text-blue-300 mt-1 inline-block">
                Buka PWA Tiket Pelanggan &rarr;
            </a>
        </div>

    </div>

</body>
</html>

<script>
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
</script>