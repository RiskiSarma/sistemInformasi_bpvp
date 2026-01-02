@extends('layouts.instructor')

@section('title', 'Catat Kehadiran - ' . $program->name)

@section('content')
<div class="space-y-8"> <!-- Tambah space lebih lega antar section -->

    <!-- Header -->
    <div>
        <h2 class="text-2xl font-bold text-gray-800">Catat Kehadiran</h2>
        <p class="text-gray-600 mt-1">{{ $program->name }} - {{ $today->format('d F Y') }}</p>
    </div>

    <!-- Card 1: Form Catat Kehadiran Hari Ini -->
    <div class="bg-white rounded-xl shadow-md border border-gray-200 overflow-hidden">
        <div class="px-6 py-5 border-b bg-gray-50">
            <h3 class="text-lg font-semibold text-gray-800">Kehadiran Hari Ini</h3>
            <p class="text-sm text-gray-500 mt-1">Catat atau update status kehadiran peserta untuk tanggal hari ini.</p>
        </div>

        <form action="{{ route('instructor.attendance.record') }}" method="POST">
            @csrf
            <input type="hidden" name="program_id" value="{{ $program->id }}">
            <input type="hidden" name="date" value="{{ $today->format('Y-m-d') }}">

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Peserta</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Catatan</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($participants as $index => $participant)
                            <?php
                                $existing = $attendances[$participant->id] ?? null;
                                $isRecorded = !is_null($existing);
                            ?>
                            <tr class="{{ $loop->last ? '' : 'hover:bg-gray-50' }}">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $loop->iteration }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ $participant->name }}
                                    @if($isRecorded)
                                        <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                            Sudah absen
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    @if($isRecorded)
                                        <span class="px-2.5 py-1 rounded-full text-xs font-medium
                                            {{ $existing->status == 'present' ? 'bg-green-100 text-green-800' :
                                               ($existing->status == 'absent' ? 'bg-red-100 text-red-800' :
                                               ($existing->status == 'late' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800')) }}">
                                            {{ ucfirst($existing->status) }}
                                        </span>
                                        @if($existing->notes)
                                            <span class="ml-2 text-xs text-gray-500">({{ Str::limit($existing->notes, 30) }})</span>
                                        @endif
                                        <!-- Hidden fields untuk update -->
                                        <input type="hidden" name="attendances[{{ $loop->index }}][status]" value="{{ $existing->status }}">
                                        <input type="hidden" name="attendances[{{ $loop->index }}][participant_id]" value="{{ $participant->id }}">
                                        <input type="hidden" name="attendances[{{ $loop->index }}][notes]" value="{{ $existing->notes }}">
                                    @else
                                        <select name="attendances[{{ $loop->index }}][status]" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                            <option value="present">Hadir</option>
                                            <option value="absent">Absen</option>
                                            <option value="late">Terlambat</option>
                                            <option value="excused">Izin</option>
                                        </select>
                                        <input type="hidden" name="attendances[{{ $loop->index }}][participant_id]" value="{{ $participant->id }}">
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    @if($isRecorded)
                                        {{ $existing->notes ?: '-' }}
                                    @else
                                        <input type="text" name="attendances[{{ $loop->index }}][notes]" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" placeholder="Opsional...">
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center text-gray-500">Tidak ada peserta aktif di program ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="px-6 py-5 bg-gray-50 border-t flex justify-end">
                <button type="submit" class="inline-flex items-center px-6 py-2.5 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition">
                    Simpan Kehadiran Hari Ini
                </button>
            </div>
        </form>
    </div>

    <!-- Card 2: Riwayat Kehadiran -->
    <div class="bg-white rounded-xl shadow-md border border-gray-200 overflow-hidden">
        <div class="px-6 py-5 border-b bg-gray-50">
            <h3 class="text-lg font-semibold text-gray-800">Riwayat Kehadiran</h3>
            <p class="text-sm text-gray-500 mt-1">Ringkasan kehadiran peserta per hari untuk program ini.</p>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hadir</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tidak Hadir</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Izin</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Terlambat</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Tercatat</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($attendanceHistory as $history)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ \Carbon\Carbon::parse($history->date)->locale('id')->translatedFormat('l, d F Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-green-700 font-medium">{{ $history->present }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-red-700 font-medium">{{ $history->absent }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-orange-700 font-medium">{{ $history->excused }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-yellow-700 font-medium">{{ $history->late }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-medium">
                                {{ $history->total }} / {{ $program->participants->count() }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-16 text-center text-gray-500 bg-gray-50">
                                <div class="flex flex-col items-center">
                                    <svg class="w-12 h-12 text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    <p class="text-base font-medium text-gray-600">Belum ada riwayat kehadiran untuk program ini.</p>
                                    <p class="text-sm text-gray-500 mt-1">Mulai catat kehadiran hari ini untuk melihat data di sini.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection