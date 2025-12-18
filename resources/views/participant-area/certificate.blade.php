@extends('layouts.participant')

@section('title', 'Sertifikat')

@section('content')
<div class="bg-white rounded-lg shadow-sm border p-6">
    <h2 class="text-2xl font-bold text-gray-800 mb-6">Sertifikat Pelatihan</h2>
    
    @if($certificates && $certificates->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @foreach($certificates as $certificate)
            <div class="border rounded-lg p-6 hover:shadow-md transition">
                <div class="flex items-start justify-between mb-4">
                    <div>
                        <h3 class="font-semibold text-gray-800">{{ $certificate->program->name ?? 'N/A' }}</h3>
                        <p class="text-sm text-gray-600">No: {{ $certificate->certificate_number }}</p>
                    </div>
                    <span class="px-3 py-1 text-xs rounded-full {{ $certificate->status === 'issued' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                        {{ ucfirst($certificate->status) }}
                    </span>
                </div>
                
                <div class="space-y-2 mb-4">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Tanggal Terbit:</span>
                        <span class="font-medium">{{ $certificate->issue_date->format('d F Y') }}</span>
                    </div>
                </div>
                
                @if($certificate->status === 'issued')
                <a href="{{ route('participant.certificate.download', $certificate->id) }}" 
                   class="block w-full text-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    <i class="fas fa-download mr-2"></i> Download Sertifikat
                </a>
                @else
                <button disabled class="block w-full text-center px-4 py-2 bg-gray-300 text-gray-600 rounded-lg cursor-not-allowed">
                    Sedang Diproses
                </button>
                @endif
            </div>
            @endforeach
        </div>
    @else
    <div class="text-center py-12">
        <svg class="w-24 h-24 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
        </svg>
        <h3 class="text-lg font-semibold text-gray-800 mb-2">Sertifikat Belum Tersedia</h3>
        <p class="text-gray-600">Sertifikat akan tersedia setelah Anda menyelesaikan pelatihan dan memenuhi syarat kelulusan</p>
        
        @if($participant)
        <div class="mt-6 inline-block">
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 text-left">
                <h4 class="font-semibold text-blue-900 mb-2">Syarat Kelulusan:</h4>
                <ul class="text-sm text-blue-800 space-y-1">
                    <li>✓ Kehadiran minimal 80%</li>
                    <li>✓ Menyelesaikan semua tugas</li>
                    <li>✓ Lulus ujian akhir</li>
                </ul>
            </div>
        </div>
        @endif
    </div>
    @endif
</div>
@endsection