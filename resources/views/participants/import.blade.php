@extends('layouts.app')

@section('title', 'Import Peserta')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <a href="{{ route('admin.participants.index') }}"
           class="text-blue-600 hover:text-blue-800 flex items-center space-x-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            <span>Kembali</span>
        </a>
    </div>

    {{-- Info Password Default --}}
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 text-sm text-blue-800">
        <strong>ℹ️ Info Akun Peserta:</strong><br>
        Peserta akan otomatis dibuatkan akun login.<br>
        Password default = <strong>NIK peserta</strong> (jika NIK diisi),
        atau <strong>Password@123</strong> jika NIK kosong.<br>
        Peserta disarankan ganti password setelah login pertama.
    </div>

    {{-- Error import --}}
    @if(session('import_errors'))
    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
        <p class="font-medium text-yellow-800 mb-2">Beberapa baris dilewati:</p>
        <ul class="list-disc list-inside text-sm text-yellow-700 space-y-1">
            @foreach(session('import_errors') as $err)
                <li>{{ $err }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="bg-white rounded-lg shadow-sm border p-6 space-y-6">
        <div>
            <h2 class="text-xl font-bold text-gray-800">Import Data Peserta dari Excel</h2>
            <p class="text-gray-500 text-sm mt-1">
                Upload file Excel/CSV berisi data peserta. Akun login akan dibuat otomatis.
            </p>
        </div>

        {{-- Download Template --}}
        <div class="bg-gray-50 rounded-lg p-4 flex items-center justify-between">
            <div>
                <p class="font-medium text-gray-700 text-sm">Belum punya template?</p>
                <p class="text-xs text-gray-500">Download template Excel lalu isi data peserta</p>
            </div>
            <a href="{{ route('admin.participants.template') }}"
               class="px-4 py-2 bg-green-600 text-white text-sm rounded-lg hover:bg-green-700 flex items-center space-x-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
                <span>Download Template</span>
            </a>
        </div>

        <form method="POST" action="{{ route('admin.participants.import') }}"
              enctype="multipart/form-data" class="space-y-5">
            @csrf

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Program Pelatihan <span class="text-red-500">*</span>
                </label>
                <select name="program_id" required
                        class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="">-- Pilih Program --</option>
                    @foreach($programs as $program)
                        <option value="{{ $program->id }}">
                            {{ $program->masterProgram?->name ?? 'Program' }}
                            — Angkatan {{ $program->angkatan ?? '-' }}
                        </option>
                    @endforeach
                </select>
                @error('program_id')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    File Excel / CSV <span class="text-red-500">*</span>
                </label>
                <input type="file" name="file" accept=".xlsx,.xls,.csv" required
                       class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500
                              @error('file') border-red-500 @enderror">
                <p class="text-xs text-gray-500 mt-1">Format: .xlsx, .xls, atau .csv. Maks 5MB.</p>
                @error('file')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Petunjuk kolom --}}
            <div class="bg-gray-50 rounded-lg p-4 text-xs text-gray-600">
                <p class="font-semibold mb-2">Kolom yang diperlukan di file Excel:</p>
                <div class="grid grid-cols-2 gap-1">
                    <span>• <code>nama</code> (wajib)</span>
                    <span>• <code>email</code> (wajib)</span>
                    <span>• <code>nik</code></span>
                    <span>• <code>telepon</code></span>
                    <span>• <code>jenis_kelamin</code></span>
                    <span>• <code>tempat_lahir</code></span>
                    <span>• <code>tanggal_lahir</code></span>
                    <span>• <code>pendidikan</code></span>
                    <span>• <code>alamat</code></span>
                    <span>• <code>status</code> (active/graduated/dropout)</span>
                </div>
            </div>

            <div class="flex justify-end space-x-3 pt-4 border-t">
                <a href="{{ route('admin.participants.index') }}"
                   class="px-4 py-2 border rounded-lg hover:bg-gray-50">Batal</a>
                <button type="submit"
                        class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    Import Sekarang
                </button>
            </div>
        </form>
    </div>
</div>
@endsection