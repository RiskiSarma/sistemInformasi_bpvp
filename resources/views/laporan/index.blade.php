<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Laporan') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Pilih Jenis Laporan</h3>
                    <p class="text-gray-600 mb-6">Silakan pilih jenis laporan yang ingin Anda lihat</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Laporan Siswa -->
                <a href="{{ route('laporan.siswa') }}" class="bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-md transform hover:scale-105 transition duration-300">
                    <div class="p-6">
                        <div class="flex items-center space-x-4 mb-4">
                            <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center">
                                <i class="fas fa-users text-white text-2xl"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-gray-800">Laporan Siswa</h3>
                                <p class="text-gray-600 text-sm">Data dan statistik siswa</p>
                            </div>
                        </div>
                        <div class="flex items-center text-blue-600 font-semibold">
                            <span>Lihat Laporan</span>
                            <i class="fas fa-arrow-right ml-2"></i>
                        </div>
                    </div>
                </a>

                <!-- Laporan Pelatihan -->
                <a href="{{ route('laporan.pelatihan') }}" class="bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-md transform hover:scale-105 transition duration-300">
                    <div class="p-6">
                        <div class="flex items-center space-x-4 mb-4">
                            <div class="w-16 h-16 bg-gradient-to-br from-green-500 to-green-600 rounded-lg flex items-center justify-center">
                                <i class="fas fa-book-open text-white text-2xl"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-gray-800">Laporan Pelatihan</h3>
                                <p class="text-gray-600 text-sm">Data program pelatihan</p>
                            </div>
                        </div>
                        <div class="flex items-center text-green-600 font-semibold">
                            <span>Lihat Laporan</span>
                            <i class="fas fa-arrow-right ml-2"></i>
                        </div>
                    </div>
                </a>

                <!-- Laporan Sertifikat -->
                <a href="{{ route('laporan.sertifikat') }}" class="bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-md transform hover:scale-105 transition duration-300">
                    <div class="p-6">
                        <div class="flex items-center space-x-4 mb-4">
                            <div class="w-16 h-16 bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-lg flex items-center justify-center">
                                <i class="fas fa-certificate text-white text-2xl"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-gray-800">Laporan Sertifikat</h3>
                                <p class="text-gray-600 text-sm">Data penerbitan sertifikat</p>
                            </div>
                        </div>
                        <div class="flex items-center text-yellow-600 font-semibold">
                            <span>Lihat Laporan</span>
                            <i class="fas fa-arrow-right ml-2"></i>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Statistik Ringkasan -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mt-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Statistik Keseluruhan</h3>
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div class="bg-blue-50 p-4 rounded-lg">
                            <p class="text-blue-600 text-sm font-semibold">Total Siswa Aktif</p>
                            <p class="text-2xl font-bold text-gray-800 mt-2">150</p>
                        </div>
                        <div class="bg-green-50 p-4 rounded-lg">
                            <p class="text-green-600 text-sm font-semibold">Pelatihan Berjalan</p>
                            <p class="text-2xl font-bold text-gray-800 mt-2">8</p>
                        </div>
                        <div class="bg-yellow-50 p-4 rounded-lg">
                            <p class="text-yellow-600 text-sm font-semibold">Sertifikat Terbit</p>
                            <p class="text-2xl font-bold text-gray-800 mt-2">89</p>
                        </div>
                        <div class="bg-purple-50 p-4 rounded-lg">
                            <p class="text-purple-600 text-sm font-semibold">Instruktur Aktif</p>
                            <p class="text-2xl font-bold text-gray-800 mt-2">25</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @endpush
</x-app-layout>