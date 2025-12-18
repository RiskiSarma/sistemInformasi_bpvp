@extends('layouts.instructor')

@section('title', 'Catat Kehadiran - ' . $program->name)

@section('content')
<div class="space-y-6">
    <div>
        <h2 class="text-2xl font-bold text-gray-800">Catat Kehadiran</h2>
        <p class="text-gray-600 mt-1">{{ $program->name }} - {{ $today->format('d F Y') }}</p>
    </div>

    <div class="bg-white rounded-lg shadow-sm border">
        <form action="{{ route('instructor.attendance.record') }}" method="POST">
            @csrf
            <input type="hidden" name="program_id" value="{{ $program->id }}">
            <input type="hidden" name="date" value="{{ $today->format('Y-m-d') }}">

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">No</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Peserta</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Catatan</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($participants as $index => $participant)
                        <tr>
                            <td class="px-6 py-4 text-sm text-gray-900">{{ $loop->iteration }}</td>
                            <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $participant->name }}</td>
                            <td class="px-6 py-4">
                                <select name="attendances[{{ $loop->index }}][status]" class="px-3 py-1 border rounded-lg focus:ring-2 focus:ring-blue-500">
                                    <option value="present" {{ ($attendances[$participant->id] ?? null)?->status == 'present' ? 'selected' : '' }}>Hadir</option>
                                    <option value="absent" {{ ($attendances[$participant->id] ?? null)?->status == 'absent' ? 'selected' : '' }}>Absen</option>
                                    <option value="late" {{ ($attendances[$participant->id] ?? null)?->status == 'late' ? 'selected' : '' }}>Terlambat</option>
                                    <option value="excused" {{ ($attendances[$participant->id] ?? null)?->status == 'excused' ? 'selected' : '' }}>Izin</option>
                                </select>
                                <input type="hidden" name="attendances[{{ $loop->index }}][participant_id]" value="{{ $participant->id }}">
                            </td>
                            <td class="px-6 py-4">
                                <input type="text" name="attendances[{{ $loop->index }}][notes]" value="{{ ($attendances[$participant->id] ?? null)?->notes }}" placeholder="Catatan opsional..." class="w-full px-3 py-1 border rounded-lg">
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="p-6 border-t flex justify-end">
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    Simpan Kehadiran
                </button>
            </div>
        </form>
    </div>
</div>
@endsection