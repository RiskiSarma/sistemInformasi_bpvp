<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Pengaturan Sistem') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                {{ session('success') }}
            </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Sidebar Menu -->
                <div class="lg:col-span-1">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">Menu Pengaturan</h3>
                            <nav class="space-y-2">
                                <a href="#umum" class="block px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-lg">
                                    <i class="fas fa-cog mr-2"></i>Pengaturan Umum
                                </a>
                                <a href="#sistem" class="block px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-lg">
                                    <i class="fas fa-server mr-2"></i>Pengaturan Sistem
                                </a>
                                <a href="#notifikasi" class="block px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-lg">
                                    <i class="fas fa-bell mr-2"></i>Notifikasi
                                </a>
                                <a href="#backup" class="block px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-lg">
                                    <i class="fas fa-database mr-2"></i>Backup & Restore
                                </a>
                            </nav>
                        </div>
                    </div>
                </div>

                <!-- Main Content -->
                <div class="lg:col-span-2">
                    <form method="POST" action="{{ route('pengaturan.update') }}">
                        @csrf
                        
                        <!-- Pengaturan Umum -->
                        <div id="umum" class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                            <div class="p-6">
                                <h3 class="text-lg font-semibold text-gray-800 mb-4">Pengaturan Umum</h3>
                                
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Nama Aplikasi</label>
                                        <input type="text" name="app_name" value="Sistem Informasi BLK" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Email Kontak</label>
                                        <input type="email" name="contact_email" value="admin@blk.com" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">No. Telepon</label>
                                        <input type="text" name="phone" value="+62 21 1234567" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Alamat</label>
                                        <textarea name="address" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">Jl. Pendidikan No. 123, Jakarta</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Pengaturan Sistem -->
                        <div id="sistem" class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                            <div class="p-6">
                                <h3 class="text-lg font-semibold text-gray-800 mb-4">Pengaturan Sistem</h3>
                                
                                <div class="space-y-4">
                                    <div>
                                        <label class="flex items-center">
                                            <input type="checkbox" name="maintenance_mode" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                            <span class="ml-2 text-sm text-gray-700">Mode Maintenance</span>
                                        </label>
                                        <p class="text-xs text-gray-500 ml-6 mt-1">Aktifkan untuk menonaktifkan akses sementara</p>
                                    </div>

                                    <div>
                                        <label class="flex items-center">
                                            <input type="checkbox" name="registration_enabled" checked class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                            <span class="ml-2 text-sm text-gray-700">Izinkan Pendaftaran Baru</span>
                                        </label>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Batas Upload File (MB)</label>
                                        <input type="number" name="max_upload_size" value="10" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Notifikasi -->
                        <div id="notifikasi" class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                            <div class="p-6">
                                <h3 class="text-lg font-semibold text-gray-800 mb-4">Pengaturan Notifikasi</h3>
                                
                                <div class="space-y-4">
                                    <div>
                                        <label class="flex items-center">
                                            <input type="checkbox" name="email_notifications" checked class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                            <span class="ml-2 text-sm text-gray-700">Notifikasi Email</span>
                                        </label>
                                    </div>

                                    <div>
                                        <label class="flex items-center">
                                            <input type="checkbox" name="new_registration_alert" checked class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                            <span class="ml-2 text-sm text-gray-700">Notifikasi Pendaftaran Baru</span>
                                        </label>
                                    </div>

                                    <div>
                                        <label class="flex items-center">
                                            <input type="checkbox" name="certificate_notification" checked class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                            <span class="ml-2 text-sm text-gray-700">Notifikasi Penerbitan Sertifikat</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Backup & Restore -->
                        <div id="backup" class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                            <div class="p-6">
                                <h3 class="text-lg font-semibold text-gray-800 mb-4">Backup & Restore</h3>
                                
                                <div class="space-y-4">
                                    <div class="bg-blue-50 p-4 rounded-lg">
                                        <p class="text-sm text-gray-700 mb-3"><strong>Backup Terakhir:</strong> 28 Nov 2024, 10:30 WIB</p>
                                        <div class="flex space-x-2">
                                            <button type="button" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm">
                                                <i class="fas fa-download mr-2"></i>Backup Sekarang
                                            </button>
                                            <button type="button" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm">
                                                <i class="fas fa-upload mr-2"></i>Restore
                                            </button>
                                        </div>
                                    </div>

                                    <div>
                                        <label class="flex items-center">
                                            <input type="checkbox" name="auto_backup" checked class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                            <span class="ml-2 text-sm text-gray-700">Backup Otomatis (Setiap Hari)</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <div class="flex justify-end space-x-3">
                                    <button type="button" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                                        Batal
                                    </button>
                                    <button type="submit" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg">
                                        <i class="fas fa-save mr-2"></i>Simpan Pengaturan
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @endpush
</x-app-layout>