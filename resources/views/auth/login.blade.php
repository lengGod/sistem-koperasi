<!DOCTYPE html>
<html class="light" lang="id">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Login | Koperasi Digital</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&amp;display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    "colors": {
                        "outline-variant": "#c3c6d6",
                        "on-primary-container": "#c4d2ff",
                        "on-error": "#ffffff",
                        "on-primary-fixed-variant": "#0040a2",
                        "surface-variant": "#d6e3ff",
                        "on-secondary-container": "#003179",
                        "on-error-container": "#93000a",
                        "surface-container-highest": "#d6e3ff",
                        "primary-fixed-dim": "#b2c5ff",
                        "error-container": "#ffdad6",
                        "secondary-fixed": "#d9e2ff",
                        "error": "#ba1a1a",
                        "secondary-fixed-dim": "#b1c6ff",
                        "on-surface-variant": "#434654",
                        "surface-container-high": "#dfe8ff",
                        "surface-bright": "#f9f9ff",
                        "secondary-container": "#709bfe",
                        "on-primary": "#ffffff",
                        "surface-container-low": "#f0f3ff",
                        "tertiary-container": "#a33500",
                        "on-tertiary": "#ffffff",
                        "primary-container": "#0052cc",
                        "tertiary-fixed-dim": "#ffb59b",
                        "on-primary-fixed": "#001848",
                        "on-surface": "#091c35",
                        "inverse-surface": "#20314b",
                        "outline": "#737685",
                        "on-secondary": "#ffffff",
                        "on-secondary-fixed-variant": "#00419d",
                        "tertiary-fixed": "#ffdbcf",
                        "on-background": "#091c35",
                        "on-secondary-fixed": "#001946",
                        "on-tertiary-fixed-variant": "#812800",
                        "surface-container-lowest": "#ffffff",
                        "background": "#f9f9ff",
                        "tertiary": "#7b2600",
                        "surface-dim": "#cadbfc",
                        "inverse-primary": "#b2c5ff",
                        "primary-fixed": "#dae2ff",
                        "inverse-on-surface": "#ecf0ff",
                        "on-tertiary-container": "#ffc6b2",
                        "surface-tint": "#0c56d0",
                        "on-tertiary-fixed": "#380d00",
                        "secondary": "#285ab9",
                        "primary": "#003d9b",
                        "surface": "#f9f9ff",
                        "surface-container": "#e7eeff"
                    },
                    "borderRadius": {
                        "DEFAULT": "0.25rem",
                        "lg": "0.5rem",
                        "xl": "0.75rem",
                        "full": "9999px"
                    },
                    "spacing": {
                        "container-max": "1440px",
                        "stack-sm": "8px",
                        "gutter": "24px",
                        "stack-md": "16px",
                        "stack-lg": "32px",
                        "topbar-height": "64px",
                        "sidebar-width": "260px",
                        "margin-mobile": "16px"
                    },
                    "fontFamily": {
                        "headline-md": ["Inter"],
                        "label-md": ["Inter"],
                        "display-lg": ["Inter"],
                        "headline-lg-mobile": ["Inter"],
                        "headline-lg": ["Inter"],
                        "body-lg": ["Inter"],
                        "body-md": ["Inter"]
                    },
                    "fontSize": {
                        "headline-md": ["20px", {"lineHeight": "28px", "fontWeight": "600"}],
                        "label-md": ["12px", {"lineHeight": "16px", "letterSpacing": "0.05em", "fontWeight": "600"}],
                        "display-lg": ["36px", {"lineHeight": "44px", "letterSpacing": "-0.02em", "fontWeight": "700"}],
                        "headline-lg-mobile": ["24px", {"lineHeight": "32px", "fontWeight": "600"}],
                        "headline-lg": ["28px", {"lineHeight": "36px", "letterSpacing": "-0.01em", "fontWeight": "600"}],
                        "body-lg": ["16px", {"lineHeight": "24px", "fontWeight": "400"}],
                        "body-md": ["14px", {"lineHeight": "20px", "fontWeight": "400"}]
                    }
                },
            },
        }
    </script>
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f4f5f7; }
        .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; }
        .card-elevation { box-shadow: 0px 2px 4px rgba(9, 30, 66, 0.08); }
        .login-canvas { min-height: 100vh; display: flex; align-items: center; justify-content: center; background: radial-gradient(circle at top left, #f0f3ff 0%, #f9f9ff 100%); }
    </style>
</head>
<body class="bg-background text-on-background">
    <main class="login-canvas px-margin-mobile">
        <!-- Login Container -->
        <div class="w-full max-w-[440px] bg-surface-container-lowest p-stack-lg md:p-12 rounded-[24px] card-elevation flex flex-col gap-stack-lg transition-all duration-300">
            <!-- Header Section -->
            <div class="flex flex-col items-center text-center gap-stack-sm">
                <div class="w-16 h-16 bg-primary-container rounded-xl flex items-center justify-center mb-2 shadow-lg shadow-primary/20">
                    <span class="material-symbols-outlined text-white text-[40px]" style="font-variation-settings: 'FILL' 1;">account_balance</span>
                </div>
                <h1 class="font-headline-lg text-headline-lg text-on-surface tracking-tight">Koperasi Digital</h1>
                <p class="font-body-md text-body-md text-outline">Kelola ekosistem koperasi Anda dengan presisi institusional.</p>
            </div>

            <!-- Form Section -->
            <form class="flex flex-col gap-stack-md" method="POST" action="{{ route('login') }}">
                @csrf
                <!-- Username/Email Field -->
                <div class="flex flex-col gap-1.5">
                    <label class="font-label-md text-label-md text-on-surface-variant" for="email">USERNAME ATAU EMAIL</label>
                    <div class="relative group">
                        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline group-focus-within:text-primary transition-colors">person</span>
                        <input class="w-full pl-10 pr-4 py-3 bg-surface-container-low border border-outline-variant rounded-lg font-body-md text-body-md focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all" id="email" name="email" value="{{ old('email') }}" placeholder="admin@koperasi.id" required type="email"/>
                    </div>
                    @error('email') <p class="text-error text-sm mt-1">{{ $message }}</p> @enderror
                </div>
                <!-- Password Field -->
                <div class="flex flex-col gap-1.5">
                    <div class="flex justify-between items-center">
                        <label class="font-label-md text-label-md text-on-surface-variant" for="password">KATA SANDI</label>
                        @if (Route::has('password.request'))
                        <a class="font-label-md text-label-md text-primary hover:underline transition-all" href="{{ route('password.request') }}">Lupa kata sandi?</a>
                        @endif
                    </div>
                    <div class="relative group">
                        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline group-focus-within:text-primary transition-colors">lock</span>
                        <input class="w-full pl-10 pr-12 py-3 bg-surface-container-low border border-outline-variant rounded-lg font-body-md text-body-md focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all" id="password" name="password" placeholder="••••••••" required type="password"/>
                        <button class="absolute right-3 top-1/2 -translate-y-1/2 text-outline hover:text-on-surface transition-colors" onclick="togglePassword()" type="button">
                            <span class="material-symbols-outlined" id="eyeIcon">visibility</span>
                        </button>
                    </div>
                    @error('password') <p class="text-error text-sm mt-1">{{ $message }}</p> @enderror
                </div>
                <!-- Remember Me Checkbox -->
                <div class="flex items-center gap-2 py-2">
                    <input class="w-4 h-4 rounded border-outline-variant text-primary focus:ring-primary" id="remember" name="remember" type="checkbox"/>
                    <label class="font-body-md text-body-md text-on-surface-variant cursor-pointer" for="remember">Ingat perangkat ini selama 30 hari</label>
                </div>
                <!-- Submit Button -->
                <button class="mt-2 w-full bg-primary-container hover:bg-primary text-white font-headline-md text-headline-md py-3.5 rounded-lg shadow-md active:scale-[0.98] transition-all duration-150 flex items-center justify-center gap-2 group" type="submit">
                    <span>Masuk ke Portal</span>
                    <span class="material-symbols-outlined text-[20px] group-hover:translate-x-1 transition-transform">arrow_forward</span>
                </button>
            </form>
            <!-- Footer Section -->
            <div class="pt-stack-md border-t border-outline-variant/30 flex flex-col gap-4 text-center">
                <p class="font-body-md text-body-md text-on-surface-variant">
                    Butuh bantuan? <a class="text-primary font-semibold hover:underline" href="#">Hubungi Dukungan</a>
                </p>
                <div class="flex justify-center gap-stack-md text-outline">
                    <span class="material-symbols-outlined text-[18px]">verified_user</span>
                    <span class="font-label-md text-label-md">ENKRIPSI PERUSAHAAN AMAN</span>
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
