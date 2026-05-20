<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon">
    <title>Login - Sistem Informasi BLK</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Syne:wght@700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="icon" type="image/png" href="{{ asset('images/logo blk banda.png') }}">
    <style>
        :root {
            --navy:   #0b1f4b;
            --teal:   #0d9488;
            --teal-l: #ccfbf1;
            --teal-d: #0f766e;
            --gold:   #f59e0b;
            --ink:    #0f172a;
            --muted:  #64748b;
            --line:   #e2e8f0;
        }
        
        * {
            font-family: 'Plus Jakarta Sans', sans-serif;
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        
        .font-display { font-family: 'Syne', sans-serif; }
        
        body {
            background:
                radial-gradient(ellipse at 70% 30%, rgba(13,148,136,.15) 0%, transparent 55%),
                radial-gradient(ellipse at 10% 80%, rgba(245,158,11,.08) 0%, transparent 45%),
                linear-gradient(150deg, #071330 0%, #0b1f4b 45%, #0f3460 100%);
            min-height: 100vh;
            position: relative;
        }
        
        .bg-grid {
            position: fixed;
            inset: 0;
            background-image:
                linear-gradient(rgba(255,255,255,.03) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,.03) 1px, transparent 1px);
            background-size: 48px 48px;
            pointer-events: none;
            z-index: 0;
        }
        
        .login-card {
            background: rgba(255, 255, 255, 0.97);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        }
        
        .input-field {
            transition: all 0.3s ease;
        }
        
        .input-field:focus {
            border-color: var(--teal);
            box-shadow: 0 0 0 3px rgba(13, 148, 136, 0.1);
            outline: none;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, var(--teal), #14b8a6);
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            background: linear-gradient(135deg, var(--teal-d), var(--teal));
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(13, 148, 136, 0.4);
        }
        
        .logo-box {
            width: 90px;
            height: 90px;
            background: linear-gradient(135deg, rgba(13,148,136,.1), rgba(13,148,136,.05));
            border: 2px solid rgba(13,148,136,.2);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto;
        }

        .decorative-line {
            width: 60px;
            height: 4px;
            background: linear-gradient(90deg, var(--teal), #5eead4);
            border-radius: 999px;
            margin: 0 auto;
        }

        .link-teal {
            color: var(--teal);
            transition: color 0.2s;
        }

        .link-teal:hover {
            color: var(--teal-d);
        }
    </style>
</head>
<body class="antialiased">
    <div class="bg-grid"></div>
    
    <div class="min-h-screen flex items-center justify-center p-4 relative z-10">
        <div class="w-full max-w-md">
            <!-- Card Login -->
            <div class="login-card rounded-2xl p-8 md:p-10">
                <!-- Logo & Header -->
                <div class="text-center mb-8">
                    <div class="logo-box mb-5">
                        <img src="images/logo blk banda.png" alt="Logo BLK" class="h-16 w-auto">
                    </div>
                    <h1 class="font-display text-3xl font-bold mb-2" style="color: var(--navy); letter-spacing: -0.02em;">
                        Selamat Datang
                    </h1>
                    <div class="decorative-line mb-3"></div>
                    <p class="text-sm" style="color: var(--muted);">Masuk ke Sistem Informasi BLK Banda Aceh</p>
                </div>

                <!-- Session Status -->
                @if (session('status'))
                    <div class="mb-5 p-4 bg-emerald-50 border-l-4 border-emerald-500 text-emerald-700 rounded-lg text-sm">
                        <i class="fas fa-check-circle mr-2"></i>{{ session('status') }}
                    </div>
                @endif

                <!-- Error Messages -->
                @if ($errors->any())
                    <div class="mb-5 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 rounded-lg">
                        <div class="flex items-start">
                            <i class="fas fa-exclamation-circle mt-0.5 mr-2"></i>
                            <div class="text-sm">
                                @foreach ($errors->all() as $error)
                                    <p class="mb-1 last:mb-0">{{ $error }}</p>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Form Login -->
                <form method="POST" action="{{ route('login') }}" class="space-y-5">
                    @csrf

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-semibold mb-2" style="color: var(--ink);">
                            Email
                        </label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2" style="color: var(--muted);">
                                <i class="fas fa-envelope text-sm"></i>
                            </span>
                            <input 
                                id="email" 
                                type="email" 
                                name="email" 
                                value="{{ old('email') }}"
                                required 
                                autofocus 
                                autocomplete="username"
                                class="input-field w-full pl-12 pr-4 py-3 rounded-xl border-2 border-gray-200 text-sm"
                                placeholder="nama@email.com"
                                style="color: var(--ink);"
                            />
                        </div>
                    </div>

                    <!-- Password -->
                    <div>
                        <label for="password" class="block text-sm font-semibold mb-2" style="color: var(--ink);">
                            Password
                        </label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2" style="color: var(--muted);">
                                <i class="fas fa-lock text-sm"></i>
                            </span>
                            <input 
                                id="password" 
                                type="password" 
                                name="password" 
                                required 
                                autocomplete="current-password"
                                class="input-field w-full pl-12 pr-12 py-3 rounded-xl border-2 border-gray-200 text-sm"
                                placeholder="Masukkan password"
                                style="color: var(--ink);"
                            />
                            <button 
                                type="button" 
                                onclick="togglePassword()"
                                class="absolute right-4 top-1/2 -translate-y-1/2 transition-colors"
                                style="color: var(--muted);"
                                onmouseover="this.style.color='var(--teal)'"
                                onmouseout="this.style.color='var(--muted)'"
                            >
                                <i id="toggleIcon" class="fas fa-eye text-sm"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Remember Me & Forgot Password -->
                    <div class="flex items-center justify-between">
                        <label class="flex items-center cursor-pointer group">
                            <input 
                                id="remember_me" 
                                type="checkbox" 
                                name="remember"
                                class="w-4 h-4 rounded border-gray-300 cursor-pointer"
                                style="color: var(--teal); accent-color: var(--teal);"
                            >
                            <span class="ml-2 text-sm transition-colors" style="color: var(--muted);">Ingat Saya</span>
                        </label>

                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="text-sm font-semibold link-teal">
                                Lupa Password?
                            </a>
                        @endif
                    </div>

                    <!-- Button Login -->
                    <button 
                        type="submit"
                        class="btn-primary w-full text-white font-bold py-3 rounded-xl shadow-lg text-sm"
                    >
                        <i class="fas fa-sign-in-alt mr-2"></i>
                        Masuk ke Dashboard
                    </button>
                </form>

                <!-- Divider -->
                <div class="relative my-6">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t-2 border-gray-100"></div>
                    </div>
                    <div class="relative flex justify-center text-xs">
                        <span class="px-4 bg-white" style="color: var(--muted);">atau</span>
                    </div>
                </div>

                <!-- Link Register -->
                <div class="text-center">
                    <p class="text-sm" style="color: var(--muted);">
                        Belum punya akun? 
                        <a href="{{ route('register') }}" class="font-bold link-teal">
                            Daftar sekarang
                        </a>
                    </p>
                </div>

                <!-- Link Home -->
                <div class="mt-6 text-center">
                    <a href="{{ url('/') }}" class="inline-flex items-center text-sm transition-colors link-teal">
                        <i class="fas fa-arrow-left mr-2 text-xs"></i>
                        Kembali ke Beranda
                    </a>
                </div>
            </div>

            <!-- Footer -->
            <div class="mt-6 text-center">
                <p class="text-xs" style="color: rgba(255,255,255,0.5);">
                    © {{ date('Y') }} Balai Latihan Kerja Banda Aceh
                </p>
            </div>
        </div>
    </div>

    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.getElementById('toggleIcon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            }
        }
    </script>
</body>
</html>