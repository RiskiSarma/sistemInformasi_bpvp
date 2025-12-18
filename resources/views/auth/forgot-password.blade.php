<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Password - Sistem Informasi BLK</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .reset-card {
            backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.95);
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        <!-- Card Reset Password -->
        <div class="reset-card rounded-2xl shadow-2xl p-8">
            <!-- Header -->
            <div class="text-center mb-8">
                <div class="w-20 h-20 bg-gradient-to-br from-yellow-500 to-orange-600 rounded-full mx-auto mb-4 flex items-center justify-center">
                    <i class="fas fa-key text-white text-3xl"></i>
                </div>
                <h2 class="text-3xl font-bold text-gray-800">Lupa Password?</h2>
                <p class="text-gray-600 mt-2">Jangan khawatir, kami akan bantu!</p>
            </div>

            <!-- Info Text -->
            <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-6 rounded">
                <div class="flex items-start">
                    <i class="fas fa-info-circle text-blue-500 mt-1 mr-3"></i>
                    <p class="text-sm text-gray-700">
                        Masukkan email Anda dan kami akan mengirimkan link untuk reset password ke email tersebut.
                    </p>
                </div>
            </div>

            <!-- Session Status (Success Message) -->
            @if (session('status'))
                <div class="mb-6 p-4 bg-green-100 border border-green-400 rounded-lg">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle text-green-600 mr-3"></i>
                        <p class="text-green-700 text-sm">{{ session('status') }}</p>
                    </div>
                </div>
            @endif

            <!-- Form Reset Password -->
            <form method="POST" action="{{ route('password.email') }}">
                @csrf

                <!-- Email -->
                <div class="mb-6">
                    <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-envelope mr-2"></i>Email Terdaftar
                    </label>
                    <input 
                        id="email" 
                        type="email" 
                        name="email" 
                        value="{{ old('email') }}"
                        required 
                        autofocus
                        class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition duration-200 outline-none"
                        placeholder="nama@email.com"
                    />
                    @error('email')
                        <p class="mt-2 text-sm text-red-600">
                            <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Button Submit -->
                <button 
                    type="submit"
                    class="w-full bg-gradient-to-r from-yellow-500 to-orange-600 text-white font-semibold py-3 rounded-lg hover:from-yellow-600 hover:to-orange-700 transform hover:scale-[1.02] transition duration-200 shadow-lg"
                >
                    <i class="fas fa-paper-plane mr-2"></i>Kirim Link Reset Password
                </button>
            </form>

            <!-- Back to Login -->
            <div class="mt-6 text-center">
                <a href="{{ route('login') }}" class="text-sm text-blue-600 hover:text-blue-800 font-semibold inline-flex items-center">
                    <i class="fas fa-arrow-left mr-2"></i>Kembali ke Login
                </a>
            </div>

            <!-- Footer -->
            <div class="mt-6 text-center text-sm text-gray-600">
                <p>© 2024 Balai Latihan Kerja</p>
            </div>
        </div>

        <!-- Help Text -->
        <div class="mt-6 text-center">
            <p class="text-white text-sm">
                <i class="fas fa-question-circle mr-1"></i>
                Butuh bantuan? Hubungi 
                <a href="mailto:admin@blk.ac.id" class="font-semibold underline">admin@blk.ac.id</a>
            </p>
        </div>
    </div>
</body>
</html>