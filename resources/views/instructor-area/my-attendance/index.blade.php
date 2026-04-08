@extends('layouts.instructor')

@section('title', 'Absensi Saya')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div>
        <h2 class="text-2xl font-bold text-gray-800">Absensi Mengajar</h2>
        <p class="text-gray-600 mt-1">{{ $today->locale('id')->isoFormat('dddd, D MMMM Y') }}</p>
    </div>

    <!-- Flash Messages -->
    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
            {{ session('error') }}
        </div>
    @endif

    <!-- Statistics Cards -->
    <div class="grid grid-cols-3 gap-4">
        <div class="bg-green-50 p-4 rounded-lg shadow-sm border border-green-200">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-sm text-green-600">Hadir Bulan Ini</div>
                    <div class="text-2xl font-bold text-green-600">{{ $stats['present'] }}</div>
                </div>
                <svg class="w-12 h-12 text-green-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </div>

        <div class="bg-yellow-50 p-4 rounded-lg shadow-sm border border-yellow-200">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-sm text-yellow-600">Terlambat</div>
                    <div class="text-2xl font-bold text-yellow-600">{{ $stats['late'] }}</div>
                </div>
                <svg class="w-12 h-12 text-yellow-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </div>

        <div class="bg-red-50 p-4 rounded-lg shadow-sm border border-red-200">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-sm text-red-600">Tidak Hadir</div>
                    <div class="text-2xl font-bold text-red-600">{{ $stats['absent'] }}</div>
                </div>
                <svg class="w-12 h-12 text-red-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </div>
    </div>

    <!-- Today's Schedule -->
    <div class="bg-white rounded-lg shadow-sm border">
        <div class="p-6 border-b bg-blue-50">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-2">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <h3 class="text-lg font-semibold text-gray-800">Jadwal Hari Ini</h3>
                </div>
                <a href="{{ route('instructor.my-attendance.history') }}" class="text-blue-600 hover:text-blue-800 text-sm">
                    Lihat Riwayat →
                </a>
            </div>
        </div>

        <div class="p-6">
            @if($schedules->count() > 0)
                <div class="space-y-4">
                    @foreach($schedules as $schedule)
                        <div class="border rounded-lg p-4 {{ $schedule->attendance ? 'bg-gray-50' : 'bg-white' }}">
                            <div class="flex items-center justify-between">
                                <!-- Schedule Info -->
                                <div class="flex-1">
                                    <div class="font-medium text-gray-900">
                                        {{ $schedule->program->masterProgram->name ?? 'N/A' }}
                                    </div>
                                    <div class="text-sm text-purple-600">{{ $schedule->program->batch ?? '' }}</div>
                                    <div class="flex items-center space-x-4 mt-2 text-sm text-gray-600">
                                        <div class="flex items-center space-x-1">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            <span>{{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}</span>
                                        </div>
                                        @if($schedule->room)
                                        <div class="flex items-center space-x-1">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                            </svg>
                                            <span>{{ $schedule->room }}</span>
                                        </div>
                                        @endif
                                    </div>
                                </div>

                                <!-- Attendance Actions -->
                                <div class="flex items-center space-x-3">
                                    @if($schedule->attendance)
                                        <div class="text-right">
                                            <span class="px-3 py-1 text-sm rounded-full {{ $schedule->attendance->status_badge }}">
                                                {{ $schedule->attendance->status_label }}
                                            </span>
                                            <div class="mt-2 text-sm text-gray-600">
                                                <div>Masuk: {{ $schedule->attendance->check_in ? \Carbon\Carbon::parse($schedule->attendance->check_in)->format('H:i') : '-' }}</div>
                                                <div>Keluar: {{ $schedule->attendance->check_out ? \Carbon\Carbon::parse($schedule->attendance->check_out)->format('H:i') : '-' }}</div>
                                                @if($schedule->attendance->duration)
                                                    <div class="text-xs text-gray-400">
                                                        Durasi: {{ floor($schedule->attendance->duration / 60) }}j {{ $schedule->attendance->duration % 60 }}m
                                                    </div>
                                                @endif
                                            </div>
                                        </div>

                                        @if(!$schedule->attendance->check_out && in_array($schedule->attendance->status, ['present', 'late']))
                                        <button
                                            type="button"
                                            onclick="document.getElementById('clockOutAttendanceId').value='{{ $schedule->attendance->id }}'; document.getElementById('clockOutModal').classList.remove('hidden');"
                                            class="px-4 py-2 bg-red-600 text-white text-sm rounded-lg hover:bg-red-700">
                                            Absen Keluar
                                        </button>
                                        @endif
                                    @else
                                        <div class="flex space-x-2">
                                            <!-- Form Clock In -->
                                            <form id="clockInForm-{{ $schedule->id }}"
                                                  action="{{ route('instructor.my-attendance.clock-in') }}"
                                                  method="POST">
                                                @csrf
                                                <input type="hidden" name="schedule_id" value="{{ $schedule->id }}">
                                                <input type="hidden" name="latitude"    id="lat-{{ $schedule->id }}">
                                                <input type="hidden" name="longitude"   id="lng-{{ $schedule->id }}">
                                                <button type="button"
                                                        onclick="submitClockIn({{ $schedule->id }})"
                                                        class="px-4 py-2 bg-green-600 text-white text-sm rounded-lg hover:bg-green-700">
                                                    Absen Masuk
                                                </button>
                                            </form>

                                            <button
                                                type="button"
                                                onclick="document.getElementById('leaveScheduleId').value='{{ $schedule->id }}'; document.getElementById('leaveModal').classList.remove('hidden');"
                                                class="px-4 py-2 bg-yellow-600 text-white text-sm rounded-lg hover:bg-yellow-700">
                                                Izin/Sakit
                                            </button>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12">
                    <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <h3 class="text-lg font-semibold text-gray-800 mb-2">Tidak Ada Jadwal Hari Ini</h3>
                    <p class="text-gray-600">Anda tidak memiliki jadwal mengajar hari ini</p>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal Clock Out -->
<div id="clockOutModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-lg bg-white">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Absen Keluar</h3>
        <form method="POST" action="{{ route('instructor.my-attendance.clock-out') }}">
            @csrf
            <input type="hidden" name="attendance_id" id="clockOutAttendanceId">
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Catatan (Opsional)</label>
                <textarea name="notes" rows="3"
                          class="w-full rounded-lg border border-gray-300 p-2 focus:border-blue-500 focus:outline-none"
                          placeholder="Tambahkan catatan..."></textarea>
            </div>
            <div class="flex space-x-2">
                <button type="submit" class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                    Absen Keluar
                </button>
                <button type="button"
                        onclick="document.getElementById('clockOutModal').classList.add('hidden')"
                        class="flex-1 px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                    Batal
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Leave Request -->
<div id="leaveModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-lg bg-white">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Ajukan Izin/Sakit</h3>
        <form method="POST" action="{{ route('instructor.my-attendance.leave') }}">
            @csrf
            <input type="hidden" name="schedule_id" id="leaveScheduleId">
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Jenis</label>
                <select name="leave_type" required
                        class="w-full rounded-lg border border-gray-300 p-2 focus:border-blue-500 focus:outline-none">
                    <option value="excused">Izin</option>
                    <option value="sick">Sakit</option>
                </select>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Keterangan</label>
                <textarea name="notes" rows="3" required
                          class="w-full rounded-lg border border-gray-300 p-2 focus:border-blue-500 focus:outline-none"
                          placeholder="Jelaskan alasan Anda..."></textarea>
            </div>
            <div class="flex space-x-2">
                <button type="submit" class="flex-1 px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700">
                    Kirim
                </button>
                <button type="button"
                        onclick="document.getElementById('leaveModal').classList.add('hidden')"
                        class="flex-1 px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                    Batal
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Script langsung di dalam @section('content'), BUKAN @push('scripts') --}}
{{-- Ini memastikan JS ter-render meski layout tidak punya @stack('scripts') --}}
<script>
function submitClockIn(scheduleId) {
    var form = document.getElementById('clockInForm-' + scheduleId);
    if (!form) { return; }

    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            function(pos) {
                document.getElementById('lat-' + scheduleId).value = pos.coords.latitude;
                document.getElementById('lng-' + scheduleId).value = pos.coords.longitude;
                form.submit();
            },
            function() {
                // Izin ditolak — submit tanpa koordinat
                form.submit();
            },
            { timeout: 8000 }
        );
    } else {
        form.submit();
    }
}

// Tutup modal saat klik backdrop
window.addEventListener('click', function(e) {
    var coModal = document.getElementById('clockOutModal');
    var lvModal = document.getElementById('leaveModal');
    if (e.target === coModal) coModal.classList.add('hidden');
    if (e.target === lvModal) lvModal.classList.add('hidden');
});
</script>
@endsection