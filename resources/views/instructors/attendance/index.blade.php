@extends('layouts.app')

@section('title', 'Absensi ' . $instructor->name)

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-4">
            <a href="{{ route('admin.instructors.index') }}" class="text-blue-600 hover:text-blue-800 flex items-center space-x-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                <span>Kembali</span>
            </a>
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Absensi Instruktur</h2>
                <p class="text-gray-600 mt-1">{{ $instructor->name }}</p>
            </div>
        </div>

        <!-- Month Selector -->
        <form method="GET" action="{{ route('admin.instructors.attendance', $instructor) }}" class="flex items-center space-x-2">
            <input type="month" name="month" value="{{ $month }}" 
                   onchange="this.form.submit()"
                   class="px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
        </form>
    </div>

    <!-- Instructor Info Card -->
    <div class="bg-white rounded-lg shadow-sm border p-6">
        <div class="flex items-start space-x-6">
            <div class="w-20 h-20 bg-purple-100 rounded-full flex items-center justify-center flex-shrink-0">
                <span class="text-3xl font-bold text-purple-600">{{ substr($instructor->name, 0, 1) }}</span>
            </div>
            <div class="flex-1">
                <h3 class="text-xl font-bold text-gray-800">{{ $instructor->name }}</h3>
                <p class="text-purple-600 mt-1">{{ $instructor->expertise }}</p>
                <div class="mt-3 flex items-center space-x-6 text-sm text-gray-600">
                    <div class="flex items-center space-x-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        <span>{{ $instructor->email }}</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>{{ $instructor->weekly_teaching_hours }} jam/minggu</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-6 gap-4">
        <div class="bg-white p-4 rounded-lg shadow-sm border">
            <div class="text-sm text-gray-600">Total Jadwal</div>
            <div class="text-2xl font-bold text-gray-800">{{ $monthStats['total_scheduled'] }}</div>
        </div>
        <div class="bg-green-50 p-4 rounded-lg shadow-sm border border-green-200">
            <div class="text-sm text-green-600">Hadir</div>
            <div class="text-2xl font-bold text-green-600">{{ $monthStats['total_present'] }}</div>
        </div>
        <div class="bg-yellow-50 p-4 rounded-lg shadow-sm border border-yellow-200">
            <div class="text-sm text-yellow-600">Terlambat</div>
            <div class="text-2xl font-bold text-yellow-600">{{ $monthStats['total_late'] }}</div>
        </div>
        <div class="bg-red-50 p-4 rounded-lg shadow-sm border border-red-200">
            <div class="text-sm text-red-600">Tidak Hadir</div>
            <div class="text-2xl font-bold text-red-600">{{ $monthStats['total_absent'] }}</div>
        </div>
        <div class="bg-blue-50 p-4 rounded-lg shadow-sm border border-blue-200">
            <div class="text-sm text-blue-600">Izin</div>
            <div class="text-2xl font-bold text-blue-600">{{ $monthStats['total_excused'] }}</div>
        </div>
        <div class="bg-purple-50 p-4 rounded-lg shadow-sm border border-purple-200">
            <div class="text-sm text-purple-600">Sakit</div>
            <div class="text-2xl font-bold text-purple-600">{{ $monthStats['total_sick'] }}</div>
        </div>
    </div>

    <!-- Calendar View -->
    <div class="bg-white rounded-lg shadow-sm border">
        <div class="p-6 border-b bg-blue-50">
            <div class="flex items-center space-x-2">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <h3 class="text-lg font-semibold text-gray-800">
                    Kalender Absensi - {{ \Carbon\Carbon::parse($month)->locale('id')->isoFormat('MMMM Y') }}
                </h3>
            </div>
        </div>

        <div class="p-6">
            @if(count($calendar) > 0)
            <div class="space-y-4">
                @foreach($calendar as $day)
                <div class="border rounded-lg p-4 {{ $day['schedules'][0]['attendance'] ? 'bg-gray-50' : 'bg-white' }}">
                    <!-- Date Header -->
                    <div class="flex items-center justify-between mb-3 pb-3 border-b">
                        <div class="font-semibold text-gray-800">
                            {{ $day['date']->locale('id')->isoFormat('dddd, D MMMM Y') }}
                        </div>
                        <div class="text-sm text-gray-500">
                            {{ count($day['schedules']) }} Jadwal
                        </div>
                    </div>

                    <!-- Schedules for this day -->
                    <div class="space-y-3">
                        @foreach($day['schedules'] as $item)
                            @php
                                $schedule = $item['schedule'];
                                $attendance = $item['attendance'];
                            @endphp
                            <div class="flex items-center justify-between p-3 rounded-lg {{ $attendance ? 'bg-white border' : 'bg-gray-50 border border-dashed' }}">
                                <!-- Schedule Info -->
                                <div class="flex-1">
                                    <div class="font-medium text-gray-900">
                                        {{ $schedule->program->masterProgram->name ?? 'N/A' }}
                                    </div>
                                    <div class="flex items-center space-x-4 mt-1 text-sm text-gray-600">
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

                                <!-- Attendance Status -->
                                <div class="flex items-center space-x-3">
                                    @if($attendance)
                                        <div class="text-right text-sm">
                                            <div class="text-gray-900">
                                                {{ $attendance->clock_in ? \Carbon\Carbon::parse($attendance->clock_in)->format('H:i') : '-' }}
                                                →
                                                {{ $attendance->clock_out ? \Carbon\Carbon::parse($attendance->clock_out)->format('H:i') : '-' }}
                                            </div>
                                            @if($attendance->duration > 0)
                                            <div class="text-gray-500">{{ number_format($attendance->duration, 1) }} jam</div>
                                            @endif
                                        </div>
                                        <span class="px-3 py-1 text-sm rounded-full {{ $attendance->status_badge }}">
                                            {{ $attendance->status_label }}
                                        </span>
                                        
                                        <!-- Action Buttons -->
                                        <div class="flex items-center space-x-2">
                                            <button onclick="openEditModal({{ $attendance->id }}, '{{ $attendance->clock_in ? \Carbon\Carbon::parse($attendance->clock_in)->format('H:i') : '' }}', '{{ $attendance->clock_out ? \Carbon\Carbon::parse($attendance->clock_out)->format('H:i') : '' }}', '{{ $attendance->status }}', '{{ $attendance->notes }}')"
                                                    class="text-blue-600 hover:text-blue-800" title="Edit">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                </svg>
                                            </button>
                                            <form action="{{ route('admin.instructors.attendance.destroy', $attendance) }}" method="POST" class="inline" 
                                                  onsubmit="return confirm('Yakin ingin menghapus absensi ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-800" title="Hapus">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    @else
                                        <span class="px-3 py-1 text-sm rounded-full bg-gray-200 text-gray-600">
                                            Belum Absen
                                        </span>
                                        <button onclick="openCreateModal('{{ $day['date']->format('Y-m-d') }}', {{ $schedule->id }}, '{{ $schedule->program->masterProgram->name ?? 'N/A' }}', '{{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }}-{{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}')"
                                                class="px-3 py-1 text-sm bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                                            Input Absensi
                                        </button>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="text-center py-12">
                <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <h3 class="text-lg font-semibold text-gray-800 mb-2">Tidak Ada Jadwal</h3>
                <p class="text-gray-600">Instruktur tidak memiliki jadwal pada bulan ini</p>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal Create Attendance -->
<div id="createModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-lg bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Input Absensi</h3>
            <form id="createForm" method="POST" action="{{ route('admin.instructors.attendance.store', $instructor) }}">
                @csrf
                <input type="hidden" name="instructor_id" value="{{ $instructor->id }}">
                <input type="hidden" name="schedule_id" id="createScheduleId">
                <input type="hidden" name="attendance_date" id="createAttendanceDate">
                
                <div class="mb-3 p-3 bg-gray-50 rounded-lg text-sm">
                    <div id="createScheduleInfo"></div>
                </div>

                <div class="mb-3">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Jam Masuk</label>
                    <input type="time" name="clock_in" id="createClockIn"
                           class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                </div>

                <div class="mb-3">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Jam Keluar</label>
                    <input type="time" name="clock_out" id="createClockOut"
                           class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                </div>

                <div class="mb-3">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select name="status" required class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                        <option value="present">Hadir</option>
                        <option value="late">Terlambat</option>
                        <option value="absent">Tidak Hadir</option>
                        <option value="excused">Izin</option>
                        <option value="sick">Sakit</option>
                    </select>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Catatan</label>
                    <textarea name="notes" rows="2" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"></textarea>
                </div>

                <div class="flex space-x-2">
                    <button type="submit" class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        Simpan
                    </button>
                    <button type="button" onclick="closeCreateModal()" class="flex-1 px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit Attendance -->
<div id="editModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-lg bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Edit Absensi</h3>
            <form id="editForm" method="POST">
                @csrf
                @method('PUT')
                
                <div class="mb-3">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Jam Masuk</label>
                    <input type="time" name="clock_in" id="editClockIn"
                           class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                </div>

                <div class="mb-3">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Jam Keluar</label>
                    <input type="time" name="clock_out" id="editClockOut"
                           class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                </div>

                <div class="mb-3">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select name="status" id="editStatus" required class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                        <option value="present">Hadir</option>
                        <option value="late">Terlambat</option>
                        <option value="absent">Tidak Hadir</option>
                        <option value="excused">Izin</option>
                        <option value="sick">Sakit</option>
                    </select>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Catatan</label>
                    <textarea name="notes" id="editNotes" rows="2" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"></textarea>
                </div>

                <div class="flex space-x-2">
                    <button type="submit" class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        Update
                    </button>
                    <button type="button" onclick="closeEditModal()" class="flex-1 px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function openCreateModal(date, scheduleId, programName, time) {
    document.getElementById('createScheduleId').value = scheduleId;
    document.getElementById('createAttendanceDate').value = date;
    document.getElementById('createScheduleInfo').innerHTML = `
        <div class="font-medium">${programName}</div>
        <div class="text-gray-600">${new Date(date).toLocaleDateString('id-ID', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' })}</div>
        <div class="text-gray-600">${time}</div>
    `;
    document.getElementById('createModal').classList.remove('hidden');
}

function closeCreateModal() {
    document.getElementById('createModal').classList.add('hidden');
}

function openEditModal(id, clockIn, clockOut, status, notes) {
    const form = document.getElementById('editForm');
    form.action = `/admin/instructors/attendance/${id}`;
    document.getElementById('editClockIn').value = clockIn;
    document.getElementById('editClockOut').value = clockOut;
    document.getElementById('editStatus').value = status;
    document.getElementById('editNotes').value = notes || '';
    document.getElementById('editModal').classList.remove('hidden');
}

function closeEditModal() {
    document.getElementById('editModal').classList.add('hidden');
}
</script>
@endpush
@endsection