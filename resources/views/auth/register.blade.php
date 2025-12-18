<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon">
    <title>Daftar Akun - Sistem Informasi BLK</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .register-card {
            backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.95);
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        <!-- Card Register -->
        <div class="register-card rounded-2xl shadow-2xl p-8">
            <!-- Header -->
            <div class="text-center mb-8">
                <div class="w-20 h-20 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full mx-auto mb-4 flex items-center justify-center">
                    <i class="fas fa-user-plus text-white text-3xl"></i>
                </div>
                <h2 class="text-3xl font-bold text-gray-800">Daftar Akun</h2>
                <p class="text-gray-600 mt-2">Sistem Informasi BLK - Peserta</p>
            </div>

            <!-- Info Box -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                <div class="flex items-start">
                    <i class="fas fa-info-circle text-blue-600 mt-1 mr-3"></i>
                    <div class="text-sm text-blue-800">
                        <p class="font-semibold mb-1">Informasi Pendaftaran</p>
                        <p>Anda akan terdaftar sebagai <strong>Peserta Pelatihan</strong>. Untuk role lainnya, silakan hubungi admin.</p>
                    </div>
                </div>
            </div>

            <!-- Error Messages -->
            @if ($errors->any())
                <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
                    <ul class="list-disc list-inside text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Form Register -->
            <form method="POST" action="{{ route('register') }}">
                @csrf

                <!-- Hidden role field -->
                <input type="hidden" name="role" value="participant">

                <!-- Nama -->
                <div class="mb-4">
                    <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-user mr-2"></i>Nama Lengkap
                    </label>
                    <input 
                        id="name" 
                        type="text" 
                        name="name" 
                        value="{{ old('name') }}"
                        required 
                        autofocus
                        class="w-full px-4 py-3 rounded-lg border @error('name') border-red-500 @else border-gray-300 @enderror focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition duration-200 outline-none"
                        placeholder="Masukkan nama lengkap"
                    />
                    @error('name')
                        <p class="mt-2 text-sm text-red-600">
                            <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Email -->
                <div class="mb-4">
                    <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-envelope mr-2"></i>Email
                    </label>
                    <input 
                        id="email" 
                        type="email" 
                        name="email" 
                        value="{{ old('email') }}"
                        required 
                        autocomplete="username"
                        class="w-full px-4 py-3 rounded-lg border @error('email') border-red-500 @else border-gray-300 @enderror focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition duration-200 outline-none"
                        placeholder="nama@email.com"
                    />
                    @error('email')
                        <p class="mt-2 text-sm text-red-600">
                            <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Nomor Telepon -->
                <div class="mb-4">
                    <label for="phone" class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-phone mr-2"></i>Nomor Telepon
                    </label>
                    <input 
                        id="phone" 
                        type="text" 
                        name="phone" 
                        value="{{ old('phone') }}"
                        required 
                        class="w-full px-4 py-3 rounded-lg border @error('phone') border-red-500 @else border-gray-300 @enderror focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition duration-200 outline-none"
                        placeholder="Contoh: 081234567890"
                    />
                    @error('phone')
                        <p class="mt-2 text-sm text-red-600">
                            <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- NIK (Nomor Induk Kependudukan) -->
                <div class="mb-4">
                    <label for="nik" class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-id-card mr-2"></i>NIK (Opsional)
                    </label>
                    <input 
                        id="nik" 
                        type="text" 
                        name="nik" 
                        value="{{ old('nik') }}"
                        class="w-full px-4 py-3 rounded-lg border @error('nik') border-red-500 @else border-gray-300 @enderror focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition duration-200 outline-none"
                        placeholder="Masukkan 16 digit NIK (jika ada)"
                        maxlength="16"
                    />
                    <p class="mt-1 text-xs text-gray-500">NIK bersifat opsional, tapi jika diisi harus unik.</p>
                    @error('nik')
                        <p class="mt-2 text-sm text-red-600">
                            <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Program Pelatihan -->
<div class="mb-4">
    <label for="program_id" class="block text-sm font-semibold text-gray-700 mb-2">
        <i class="fas fa-graduation-cap mr-2"></i>Program Pelatihan yang Dibuka
    </label>
    <select 
        id="program_id" 
        name="program_id" 
        required
        class="w-full px-4 py-3 rounded-lg border @error('program_id') border-red-500 @else border-gray-300 @enderror focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition duration-200 outline-none"
    >
        <option value="">-- Pilih Program yang Sedang Dibuka --</option>
        @if($programs->count() > 0)
            @foreach($programs as $program)
                <option value="{{ $program->id }}" {{ old('program_id') == $program->id ? 'selected' : '' }}>
                    {{ $program->masterProgram->name }} 
                    @if($program->batch) - Batch {{ $program->batch }} @endif
                    ({{ $program->start_date ? 'Mulai: ' . $program->start_date->format('d/m/Y') : 'Tanggal belum ditentukan' }})
                </option>
            @endforeach
        @else
            <option value="" disabled>Pendaftaran belum dibuka untuk program apapun saat ini</option>
        @endif
    </select>
    @error('program_id')
        <p class="mt-2 text-sm text-red-600">
            <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
        </p>
    @enderror
</div>
                <!-- Password -->
                <div class="mb-4">
                    <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-lock mr-2"></i>Password
                    </label>
                    <div class="relative">
                        <input 
                            id="password" 
                            type="password" 
                            name="password" 
                            required 
                            autocomplete="new-password"
                            class="w-full px-4 py-3 rounded-lg border @error('password') border-red-500 @else border-gray-300 @enderror focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition duration-200 outline-none pr-12"
                            placeholder="Minimal 8 karakter"
                        />
                        <button 
                            type="button" 
                            onclick="togglePassword('password', 'toggleIcon1')"
                            class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-700"
                        >
                            <i id="toggleIcon1" class="fas fa-eye"></i>
                        </button>
                    </div>
                    @error('password')
                        <p class="mt-2 text-sm text-red-600">
                            <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Confirm Password -->
                <div class="mb-6">
                    <label for="password_confirmation" class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-lock mr-2"></i>Konfirmasi Password
                    </label>
                    <div class="relative">
                        <input 
                            id="password_confirmation" 
                            type="password" 
                            name="password_confirmation" 
                            required 
                            autocomplete="new-password"
                            class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition duration-200 outline-none pr-12"
                            placeholder="Ulangi password"
                        />
                        <button 
                            type="button" 
                            onclick="togglePassword('password_confirmation', 'toggleIcon2')"
                            class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-700"
                        >
                            <i id="toggleIcon2" class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>

                <!-- Button Register -->
                <button 
                    type="submit"
                    class="w-full bg-gradient-to-r from-blue-500 to-purple-600 text-white font-semibold py-3 rounded-lg hover:from-blue-600 hover:to-purple-700 transform hover:scale-[1.02] transition duration-200 shadow-lg"
                >
                    <i class="fas fa-user-plus mr-2"></i>Daftar Sekarang
                </button>
            </form>

            <!-- Link ke Login -->
            <div class="mt-6 text-center">
                <p class="text-sm text-gray-600">
                    Sudah punya akun? 
                    <a href="{{ route('login') }}" class="text-blue-600 hover:text-blue-800 font-semibold">
                        Masuk di sini
                    </a>
                </p>
            </div>

            <!-- Link ke Home -->
            <div class="mt-4 text-center">
                <a href="{{ url('/') }}" class="text-sm text-gray-600 hover:text-gray-800">
                    <i class="fas fa-home mr-1"></i>Kembali ke Beranda
                </a>
            </div>

            <!-- Footer -->
            <div class="mt-6 text-center text-sm text-gray-600">
                <p>© 2025 Balai Latihan Kerja Banda Aceh</p>
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
    </script>
</body>
</html>