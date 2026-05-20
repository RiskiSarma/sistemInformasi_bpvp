<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon">
    <title>Daftar Akun - Sistem Informasi BLK</title>
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
        
        .register-card {
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

        .input-field.error {
            border-color: #ef4444;
            box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1);
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

        .info-box {
            background: linear-gradient(135deg, rgba(13,148,136,.08), rgba(13,148,136,.05));
            border-left: 3px solid var(--teal);
        }

        .error-message {
            display: none;
            color: #ef4444;
            font-size: 0.75rem;
            margin-top: 0.375rem;
        }

        .error-message.show {
            display: flex;
            align-items: center;
            gap: 0.25rem;
        }
    </style>
</head>
<body class="antialiased">
    <div class="bg-grid"></div>
    
    <div class="min-h-screen flex items-center justify-center p-4 py-12 relative z-10">
        <div class="w-full max-w-lg">
            <!-- Card Register -->
            <div class="register-card rounded-2xl p-8 md:p-10">
                <!-- Logo & Header -->
                <div class="text-center mb-7">
                    <div class="logo-box mb-5">
                        <img src="images/logo blk banda.png" alt="Logo BLK" class="h-16 w-auto">
                    </div>
                    <h1 class="font-display text-3xl font-bold mb-2" style="color: var(--navy); letter-spacing: -0.02em;">
                        Daftar Akun Baru
                    </h1>
                    <div class="decorative-line mb-3"></div>
                    <p class="text-sm" style="color: var(--muted);">Bergabung dengan Sistem Informasi BLK</p>
                </div>

                <!-- Info Box -->
                <div class="info-box rounded-xl p-4 mb-6">
                    <div class="flex items-start gap-3">
                        <i class="fas fa-info-circle text-lg" style="color: var(--teal); margin-top: 2px;"></i>
                        <div class="text-xs leading-relaxed" style="color: var(--ink);">
                            <p class="font-semibold mb-1">Informasi Pendaftaran</p>
                            <p style="color: var(--muted);">Anda akan terdaftar sebagai <strong style="color: var(--teal);">Peserta Pelatihan</strong>. Untuk role lainnya, silakan hubungi admin.</p>
                        </div>
                    </div>
                </div>

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

                <!-- Form Register -->
                <form method="POST" action="{{ route('register') }}" class="space-y-4" id="registerForm" novalidate>
                    @csrf

                    <input type="hidden" name="role" value="participant">

                    <!-- Nama -->
                    <div>
                        <label for="name" class="block text-sm font-semibold mb-2" style="color: var(--ink);">
                            Nama Lengkap <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2" style="color: var(--muted);">
                                <i class="fas fa-user text-sm"></i>
                            </span>
                            <input 
                                id="name" 
                                type="text" 
                                name="name" 
                                value="{{ old('name') }}"
                                required 
                                autofocus
                                class="input-field w-full pl-12 pr-4 py-2.5 rounded-xl border-2 border-gray-200 text-sm"
                                placeholder="Masukkan nama lengkap"
                                style="color: var(--ink);"
                            />
                        </div>
                        <div class="error-message" id="name-error">
                            <i class="fas fa-exclamation-circle"></i>
                            <span>Nama lengkap wajib diisi</span>
                        </div>
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-semibold mb-2" style="color: var(--ink);">
                            Email <span class="text-red-500">*</span>
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
                                autocomplete="username"
                                class="input-field w-full pl-12 pr-4 py-2.5 rounded-xl border-2 border-gray-200 text-sm"
                                placeholder="nama@email.com"
                                style="color: var(--ink);"
                            />
                        </div>
                        <div class="error-message" id="email-error">
                            <i class="fas fa-exclamation-circle"></i>
                            <span>Email wajib diisi dengan format yang benar</span>
                        </div>
                    </div>

                    <!-- Nomor Telepon & NIK - Grid 2 Column -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Nomor Telepon -->
                        <div>
                            <label for="phone" class="block text-sm font-semibold mb-2" style="color: var(--ink);">
                                Nomor Telepon <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2" style="color: var(--muted);">
                                    <i class="fas fa-phone text-sm"></i>
                                </span>
                                <input 
                                    id="phone" 
                                    type="text" 
                                    name="phone" 
                                    value="{{ old('phone') }}"
                                    required 
                                    class="input-field w-full pl-12 pr-4 py-2.5 rounded-xl border-2 border-gray-200 text-sm"
                                    placeholder="081234567890"
                                    style="color: var(--ink);"
                                />
                            </div>
                            <div class="error-message" id="phone-error">
                                <i class="fas fa-exclamation-circle"></i>
                                <span>Nomor telepon wajib diisi</span>
                            </div>
                        </div>

                        <!-- NIK -->
                        <div>
                            <label for="nik" class="block text-sm font-semibold mb-2" style="color: var(--ink);">
                                NIK <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2" style="color: var(--muted);">
                                    <i class="fas fa-id-card text-sm"></i>
                                </span>
                                <input 
                                    id="nik" 
                                    type="text" 
                                    name="nik" 
                                    value="{{ old('nik') }}"
                                    required 
                                    minlength="16"
                                    maxlength="16"
                                    pattern="\d{16}"
                                    title="NIK harus 16 digit angka"
                                    class="input-field w-full pl-12 pr-4 py-2.5 rounded-xl border-2 border-gray-200 text-sm"
                                    placeholder="16 digit NIK"
                                    style="color: var(--ink);"
                                />
                            </div>
                            <p class="text-xs mt-1.5" style="color: var(--muted);">NIK harus unik per batch/program</p>
                        </div>
                    </div>

                    <!-- Program Pelatihan -->
                    <div>
                        <label for="program_id" class="block text-sm font-semibold mb-2" style="color: var(--ink);">
                            Program Pelatihan <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2" style="color: var(--muted);">
                                <i class="fas fa-graduation-cap text-sm"></i>
                            </span>
                            <select 
                                id="program_id" 
                                name="program_id" 
                                required 
                                class="input-field w-full pl-12 pr-10 py-2.5 rounded-xl border-2 border-gray-200 text-sm appearance-none bg-white cursor-pointer"
                                style="color: var(--ink);"
                            >
                                <option value="">-- Pilih Program yang Sedang Dibuka --</option>
                                @if($programs->count() > 0)
                                    @foreach($programs as $program)
                                        <option value="{{ $program->id }}" {{ old('program_id') == $program->id ? 'selected' : '' }}>
                                            {{ $program->masterProgram->name }} 
                                            @if($program->batch) - {{ $program->batch }} @endif
                                            @if($program->angkatan) - Angkatan {{ $program->angkatan }} @endif
                                            (Mulai: {{ $program->start_date ? $program->start_date->format('d/m/Y') : 'TBD' }})
                                        </option>
                                    @endforeach
                                @else
                                    <option value="" disabled>Tidak ada program yang sedang dibuka pendaftaran</option>
                                @endif
                            </select>
                            <span class="absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none" style="color: var(--muted);">
                                <i class="fas fa-chevron-down text-xs"></i>
                            </span>
                        </div>
                    </div>

                    <!-- Password & Konfirmasi Password - Grid 2 Column -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Password -->
                        <div>
                            <label for="password" class="block text-sm font-semibold mb-2" style="color: var(--ink);">
                                Password <span class="text-red-500">*</span>
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
                                    autocomplete="new-password"
                                    class="input-field w-full pl-12 pr-12 py-2.5 rounded-xl border-2 border-gray-200 text-sm"
                                    placeholder="Min. 8 karakter"
                                    style="color: var(--ink);"
                                />
                                <button 
                                    type="button" 
                                    onclick="togglePassword('password', 'toggleIcon1')"
                                    class="absolute right-4 top-1/2 -translate-y-1/2 transition-colors"
                                    style="color: var(--muted);"
                                    onmouseover="this.style.color='var(--teal)'"
                                    onmouseout="this.style.color='var(--muted)'"
                                >
                                    <i id="toggleIcon1" class="fas fa-eye text-sm"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Confirm Password -->
                        <div>
                            <label for="password_confirmation" class="block text-sm font-semibold mb-2" style="color: var(--ink);">
                                Konfirmasi Password <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2" style="color: var(--muted);">
                                    <i class="fas fa-lock text-sm"></i>
                                </span>
                                <input 
                                    id="password_confirmation" 
                                    type="password" 
                                    name="password_confirmation" 
                                    required 
                                    autocomplete="new-password"
                                    class="input-field w-full pl-12 pr-12 py-2.5 rounded-xl border-2 border-gray-200 text-sm"
                                    placeholder="Ulangi password"
                                    style="color: var(--ink);"
                                />
                                <button 
                                    type="button" 
                                    onclick="togglePassword('password_confirmation', 'toggleIcon2')"
                                    class="absolute right-4 top-1/2 -translate-y-1/2 transition-colors"
                                    style="color: var(--muted);"
                                    onmouseover="this.style.color='var(--teal)'"
                                    onmouseout="this.style.color='var(--muted)'"
                                >
                                    <i id="toggleIcon2" class="fas fa-eye text-sm"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Button Register -->
                    <div class="pt-2">
                        <button 
                            type="submit"
                            class="btn-primary w-full text-white font-bold py-3 rounded-xl shadow-lg text-sm"
                        >
                            <i class="fas fa-user-plus mr-2"></i>
                            Daftar Sekarang
                        </button>
                    </div>
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

                <!-- Link Login -->
                <div class="text-center">
                    <p class="text-sm" style="color: var(--muted);">
                        Sudah punya akun? 
                        <a href="{{ route('login') }}" class="font-bold link-teal">
                            Masuk di sini
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
        function togglePassword(inputId, iconId) {
            const passwordInput = document.getElementById(inputId);
            const toggleIcon = document.getElementById(iconId);
            
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

        // Auto format NIK input (hanya angka)
        document.getElementById('nik').addEventListener('input', function(e) {
            this.value = this.value.replace(/\D/g, '');
        });

        // Auto format phone input (hanya angka)
        document.getElementById('phone').addEventListener('input', function(e) {
            this.value = this.value.replace(/\D/g, '');
        });

        // Validasi form sebelum submit
        document.getElementById('registerForm').addEventListener('submit', function(e) {
            let isValid = true;

            // Validasi Nama
            const nameInput = document.getElementById('name');
            const nameError = document.getElementById('name-error');
            if (nameInput.value.trim() === '') {
                nameInput.classList.add('error');
                nameError.classList.add('show');
                isValid = false;
            } else {
                nameInput.classList.remove('error');
                nameError.classList.remove('show');
            }

            // Validasi Email
            const emailInput = document.getElementById('email');
            const emailError = document.getElementById('email-error');
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (emailInput.value.trim() === '' || !emailRegex.test(emailInput.value)) {
                emailInput.classList.add('error');
                emailError.classList.add('show');
                isValid = false;
            } else {
                emailInput.classList.remove('error');
                emailError.classList.remove('show');
            }

            // Validasi Phone
            const phoneInput = document.getElementById('phone');
            const phoneError = document.getElementById('phone-error');
            if (phoneInput.value.trim() === '') {
                phoneInput.classList.add('error');
                phoneError.classList.add('show');
                isValid = false;
            } else {
                phoneInput.classList.remove('error');
                phoneError.classList.remove('show');
            }

            if (!isValid) {
                e.preventDefault();
                // Scroll ke field pertama yang error
                const firstError = document.querySelector('.input-field.error');
                if (firstError) {
                    firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    firstError.focus();
                }
            }
        });

        // Real-time validation saat user mengetik
        ['name', 'email', 'phone'].forEach(fieldId => {
            const input = document.getElementById(fieldId);
            const errorDiv = document.getElementById(fieldId + '-error');
            
            input.addEventListener('input', function() {
                if (this.value.trim() !== '') {
                    this.classList.remove('error');
                    errorDiv.classList.remove('show');
                }
            });

            input.addEventListener('blur', function() {
                if (this.value.trim() === '') {
                    this.classList.add('error');
                    errorDiv.classList.add('show');
                } else if (fieldId === 'email') {
                    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    if (!emailRegex.test(this.value)) {
                        this.classList.add('error');
                        errorDiv.classList.add('show');
                    }
                }
            });
        });
    </script>
</body>
</html>