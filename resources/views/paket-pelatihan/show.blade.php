@extends('layouts.app')

@section('title', 'Detail Paket Pelatihan - ' . ($paketPelatihan->jenisPelatihan->jenis_pelatihan ?? '') . ' ' . $paketPelatihan->tahun)

@section('content')
<div class="space-y-6" x-data="{ activeTab: 'info' }">
    <div class="flex items-center justify-between">
        <h2 class="text-2xl font-bold text-gray-800">
            Detail Paket Pelatihan
            <span class="text-lg font-normal text-gray-600 ml-2">
                {{ $paketPelatihan->jenisPelatihan->jenis_pelatihan ?? '-' }} • 
                {{ $paketPelatihan->tahun }} • Batch {{ \App\Helpers\Roman::convert($paketPelatihan->batch ?? 0) }}
            </span>
        </h2>
        <div class="space-x-3">
            <span class="px-4 py-2 rounded-full text-sm font-medium
                {{ $paketPelatihan->status == 'Berjalan' ? 'bg-green-100 text-green-800' : 
                   $paketPelatihan->status == 'Pendaftaran' ? 'bg-blue-100 text-blue-800' : 
                   $paketPelatihan->status == 'Selesai' ? 'bg-gray-100 text-gray-800' : 'bg-yellow-100 text-yellow-800' }}">
                {{ $paketPelatihan->status }}
            </span>
            <a href="{{ route('admin.programs.paket-pelatihan.index') }}" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 transition">
                Kembali ke Daftar
            </a>
        </div>
    </div>

    <!-- Tab Navigation -->
    <div class="border-b border-gray-200">
        <nav class="-mb-px flex space-x-8" aria-label="Tabs">
            <button @click="activeTab = 'info'" 
                    :class="activeTab === 'info' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                    class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                Informasi Umum
            </button>
            <button @click="activeTab = 'programs'" 
                    :class="activeTab === 'programs' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                    class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                Program Pelatihan ({{ $paketPelatihan->programs->count() }})
            </button>
            <button @click="activeTab = 'peserta'" 
                    :class="activeTab === 'peserta' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                    class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                Peserta ({{ $paketPelatihan->siswas->count() }})
            </button>
            <!-- Tambah tab lain nanti: Unit Kompetensi, Pengajar, Jadwal, Dokumen -->
        </nav>
    </div>

    <!-- Tab Content -->
    <div class="bg-white rounded-lg shadow-sm border p-6">
        <!-- Tab Informasi Umum -->
        <div x-show="activeTab === 'info'">
            <dl class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div>
                    <dt class="text-sm font-medium text-gray-500">Jenis Pelatihan</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $paketPelatihan->jenisPelatihan->jenis_pelatihan ?? '-' }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Tahun & Batch</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $paketPelatihan->tahun }} • Batch {{ \App\Helpers\Roman::convert($paketPelatihan->batch ?? 0) }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">JP Harian / Industri</dt>
                    <dd class="mt-1 text-sm text-gray-900">
                        {{ $paketPelatihan->jp_harian ? $paketPelatihan->jp_harian . ' jam' : '-' }} / 
                        {{ $paketPelatihan->jp_industri ? $paketPelatihan->jp_industri . ' jam' : '-' }}
                    </dd>
                </div>
                <!-- Tambahkan field tanggal-tanggal penting di sini seperti di index -->
                <!-- ... -->
                <div class="col-span-3 border-t pt-4 mt-2">
                    <dt class="text-sm font-medium text-gray-500">Dibuat Oleh</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $paketPelatihan->user->name ?? 'Sistem' }} • {{ $paketPelatihan->created_at->format('d M Y H:i') }}</dd>
                </div>
            </dl>
        </div>

        <!-- Tab Program Pelatihan -->
        <div x-show="activeTab === 'programs'">
            @if($paketPelatihan->programs->isEmpty())
                <p class="text-center text-gray-500 py-8">Belum ada program pelatihan yang ditambahkan ke paket ini.</p>
            @else
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Program</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ada Industri?</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">JP Harian</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Periode</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($paketPelatihan->programs as $program)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ $program->program_pelatihan }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <span class="{{ $program->pivot->ada_industri == 'Y' ? 'text-green-600' : 'text-red-600' }}">
                                        {{ $program->pivot->ada_industri == 'Y' ? 'Ya' : 'Tidak' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $program->pivot->jp_harian }} jam</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    {{ $program->pivot->tanggal_mulai?->format('d/m/Y') }} s/d 
                                    {{ $program->pivot->tanggal_akhir?->format('d/m/Y') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>

        <!-- Tab Peserta -->
        <div x-show="activeTab === 'peserta'">
            @if($paketPelatihan->siswas->isEmpty())
                <p class="text-center text-gray-500 py-8">Belum ada peserta terdaftar di paket ini.</p>
            @else
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">NIK</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pendidikan</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Program</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($paketPelatihan->siswas as $siswa)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $siswa->nama }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $siswa->nik }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $siswa->pendidikan->pendidikan ?? '-' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <!-- Asumsi relasi siswa punya program via paket_pelatihan_program -->
                                    {{ $siswa->paketPelatihanProgram->programPelatihan->program_pelatihan ?? '-' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>
</div>
@endsection