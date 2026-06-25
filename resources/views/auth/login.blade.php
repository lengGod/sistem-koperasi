<!DOCTYPE html>
<html class="light" lang="id">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Login | Koperasi Digital</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&amp;display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>

    @vite(['resources/css/app.scss', 'resources/js/app.js'])

    <style>
        .login-canvas {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: radial-gradient(circle at top left, #f0f3ff 0%, #f9f9ff 100%);
        }
    </style>
</head>
<body class="bg-background text-on-surface">
    <main class="login-canvas px-4 md:px-6">
        <div class="w-full max-w-[440px] bg-surface-container-lowest p-10 md:p-12 rounded-3xl shadow-md flex flex-col gap-8 transition-all duration-300">
            <div class="flex flex-col items-center text-center gap-2">
                <div class="w-16 h-16 bg-primary-container rounded-xl flex items-center justify-center mb-2 shadow-lg shadow-primary/20">
                    <span class="material-symbols-outlined icon-fill text-white text-[40px]">account_balance</span>
                </div>
                <h1 class="text-[28px] leading-9 font-semibold tracking-tight text-on-surface">Koperasi Digital</h1>
                <p class="text-sm leading-5 text-outline">Kelola ekosistem koperasi Anda dengan presisi institusional.</p>
            </div>

            <form class="flex flex-col gap-4" method="POST" action="{{ route('login') }}">
                @csrf
                <div class="flex flex-col gap-1.5">
                    <label class="text-xs font-semibold tracking-wider uppercase text-on-surface-variant" for="email">Username atau Email</label>
                    <div class="relative group">
                        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline group-focus-within:text-primary transition-colors">person</span>
                        <input
                            class="w-full pl-10 pr-4 py-3 bg-surface-container-low border border-outline-variant rounded-lg text-sm leading-5 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all"
                            id="email"
                            name="email"
                            value="{{ old('email') }}"
                            placeholder="admin@koperasi.id"
                            type="email"
                            required
                            autofocus
                        />
                    </div>
                    @error('email')
                        <p class="text-error text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex flex-col gap-1.5">
                    <div class="flex justify-between items-center">
                        <label class="text-xs font-semibold tracking-wider uppercase text-on-surface-variant" for="password">Kata Sandi</label>
                        @if (Route::has('password.request'))
                            <a class="text-xs font-semibold tracking-wider uppercase text-primary hover:underline transition-all" href="{{ route('password.request') }}">Lupa kata sandi?</a>
                        @endif
                    </div>
                    <div class="relative group">
                        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline group-focus-within:text-primary transition-colors">lock</span>
                        <input
                            class="w-full pl-10 pr-12 py-3 bg-surface-container-low border border-outline-variant rounded-lg text-sm leading-5 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all"
                            id="password"
                            name="password"
                            placeholder="••••••••"
                            type="password"
                            required
                        />
                        <button
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-outline hover:text-on-surface transition-colors"
                            onclick="togglePassword()"
                            type="button"
                            aria-label="Toggle password visibility"
                        >
                            <span class="material-symbols-outlined" id="eyeIcon">visibility</span>
                        </button>
                    </div>
                    @error('password')
                        <p class="text-error text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center gap-2 py-2">
                    <input
                        class="w-4 h-4 rounded border-outline-variant text-primary focus:ring-primary"
                        id="remember"
                        name="remember"
                        type="checkbox"
                    />
                    <label class="text-sm leading-5 text-on-surface-variant cursor-pointer" for="remember">Ingat perangkat ini selama 30 hari</label>
                </div>

                <button
                    class="mt-2 w-full bg-primary-container hover:bg-primary text-white text-xl font-semibold py-3.5 rounded-lg shadow-md active:scale-[0.98] transition-all duration-150 flex items-center justify-center gap-2 group"
                    type="submit"
                >
                    <span>Masuk ke Portal</span>
                    <span class="material-symbols-outlined text-[20px] group-hover:translate-x-1 transition-transform">arrow_forward</span>
                </button>
            </form>

            <div class="pt-4 border-t border-outline-variant/30 flex flex-col gap-4 text-center">
                <p class="text-sm leading-5 text-on-surface-variant">
                    Butuh bantuan? <a class="text-primary font-semibold hover:underline" href="#">Hubungi Dukungan</a>
                </p>
                <div class="flex justify-center items-center gap-2 text-outline">
                    <span class="material-symbols-outlined text-[18px]">verified_user</span>
                    <span class="text-xs font-semibold tracking-wider uppercase">Enkripsi Perusahaan Aman</span>
                </div>
            </div>
        </div>
    </main>

    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eyeIcon');
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.textContent = 'visibility_off';
            } else {
                passwordInput.type = 'password';
                eyeIcon.textContent = 'visibility';
            }
        }
    </script>
</body>
</html>