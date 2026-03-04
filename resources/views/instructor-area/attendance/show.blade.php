@extends('layouts.instructor')

@section('title', 'Catat Kehadiran - ' . $program->name)

@section('content')
<div class="space-y-6">

    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <a href="{{ route('instructor.attendance.index') }}"
               class="inline-flex items-center text-sm text-gray-500 hover:text-gray-800 mb-2 transition-colors">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Kembali
            </a>
            <h2 class="text-2xl font-bold text-gray-900">Catat Kehadiran</h2>
            <p class="text-gray-500 mt-0.5 text-sm">{{ $program->name }} &mdash; {{ $today->locale('id')->translatedFormat('l, d F Y') }}</p>
        </div>
        <div class="text-right hidden md:block">
            <div class="text-xs text-gray-400 uppercase tracking-wide">Total Peserta</div>
            <div class="text-3xl font-bold text-gray-900">{{ $program->participants->count() }}</div>
        </div>
    </div>

    @if(session('success'))
    <div class="bg-green-50 border border-green-200 rounded-lg p-4 flex items-center space-x-3">
        <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <p class="text-green-800 text-sm">{{ session('success') }}</p>
    </div>
    @endif
    @if(session('error'))
    <div class="bg-red-50 border border-red-200 rounded-lg p-4 flex items-center space-x-3">
        <svg class="w-5 h-5 text-red-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <p class="text-red-800 text-sm">{{ session('error') }}</p>
    </div>
    @endif

    <!-- ============================================ -->
    <!-- AT-RISK SUMMARY BANNER -->
    <!-- ============================================ -->
    @php
        $minAttendance = 80;
        $allStats = $participants->map(function($participant) use ($program, $minAttendance) {
            $records  = \App\Models\Attendance::where('program_id', $program->id)
                ->where('participant_id', $participant->id)->get();
            $total    = $records->count();
            $present  = $records->where('status', 'present')->count();
            $absent   = $records->where('status', 'absent')->count();
            $pct      = $total > 0 ? round(($present / $total) * 100) : 0;
            $lulus    = $pct >= $minAttendance;
            $maxAbsen = (int) floor($total * (1 - $minAttendance / 100));
            $sisaBolehAbsen = max(0, $maxAbsen - $absent);
            return compact('participant', 'total', 'present', 'absent', 'pct', 'lulus', 'sisaBolehAbsen');
        });
        $atRisk = $allStats->filter(fn($s) => !$s['lulus'] && $s['total'] > 0);
    @endphp

    @if($atRisk->count() > 0)
    <div class="bg-red-50 border border-red-200 rounded-xl overflow-hidden">
        <div class="px-5 py-4 border-b border-red-200 flex items-center space-x-3">
            <svg class="w-5 h-5 text-red-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            <div>
                <p class="text-sm font-bold text-red-800">
                    {{ $atRisk->count() }} peserta belum memenuhi syarat kehadiran minimum {{ $minAttendance }}%
                </p>
                <p class="text-xs text-red-600 mt-0.5">Perhatikan kehadiran peserta berikut</p>
            </div>
        </div>
        <div class="px-5 py-3">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2">
                @foreach($atRisk as $stat)
                <div class="flex items-center justify-between bg-white border border-red-200 rounded-lg px-3 py-2">
                    <div class="flex items-center space-x-2 min-w-0">
                        <div class="w-7 h-7 rounded-full bg-red-100 flex items-center justify-center flex-shrink-0">
                            <span class="text-xs font-bold text-red-700">{{ strtoupper(substr($stat['participant']->name, 0, 1)) }}</span>
                        </div>
                        <p class="text-xs font-medium text-gray-800 truncate">{{ $stat['participant']->name }}</p>
                    </div>
                    <span class="text-xs font-bold text-red-700 flex-shrink-0 ml-2">{{ $stat['pct'] }}%</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @elseif($allStats->filter(fn($s) => $s['total'] > 0)->count() > 0)
    <div class="bg-green-50 border border-green-200 rounded-xl px-5 py-4 flex items-center space-x-3">
        <svg class="w-5 h-5 text-green-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <p class="text-sm font-semibold text-green-800">
            Semua peserta sudah memenuhi syarat kehadiran minimum {{ $minAttendance }}%
        </p>
    </div>
    @endif

    <!-- ============================== -->
    <!-- FORM ABSENSI HARI INI -->
    <!-- ============================== -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b bg-gray-50 flex items-center justify-between">
            <div>
                <h3 class="text-base font-semibold text-gray-800">Kehadiran Hari Ini</h3>
                <p class="text-xs text-gray-400 mt-0.5">{{ $today->locale('id')->translatedFormat('l, d F Y') }}</p>
            </div>
            @php $recordedCount = collect($attendances)->count(); $totalCount = $participants->count(); @endphp
            <span class="text-xs text-gray-500">Tercatat: <strong class="{{ $recordedCount >= $totalCount && $totalCount > 0 ? 'text-green-600' : 'text-blue-600' }}">{{ $recordedCount }}/{{ $totalCount }}</strong></span>
        </div>

        <form action="{{ route('instructor.attendance.record') }}" method="POST">
            @csrf
            <input type="hidden" name="program_id" value="{{ $program->id }}">
            <input type="hidden" name="date" value="{{ $today->format('Y-m-d') }}">

            <!-- Quick action -->
            <div class="px-6 py-3 border-b bg-white flex items-center space-x-3">
                <span class="text-xs text-gray-500 font-medium">Tandai semua:</span>
                <button type="button" onclick="setAllStatus('present')"
                    class="px-3 py-1 text-xs bg-green-100 text-green-700 rounded-md hover:bg-green-200 font-medium transition">✓ Semua Hadir</button>
                <button type="button" onclick="setAllStatus('absent')"
                    class="px-3 py-1 text-xs bg-red-100 text-red-700 rounded-md hover:bg-red-200 font-medium transition">✗ Semua Absen</button>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead>
                        <tr class="bg-gray-50 border-b text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            <th class="px-6 py-3 text-left w-8">#</th>
                            <th class="px-6 py-3 text-left">Peserta</th>
                            <th class="px-6 py-3 text-center">Kehadiran S/D Hari Ini</th>
                            <th class="px-6 py-3 text-center">Status Kehadiran</th>
                            <th class="px-6 py-3 text-left">Catatan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($participants as $index => $participant)
                        @php
                            $existing      = $attendances[$participant->id] ?? null;
                            $isRecorded    = !is_null($existing);
                            $currentStatus = $existing ? $existing->status : 'present';
                            $pStat         = $allStats->firstWhere('participant.id', $participant->id);
                            $pLulus        = $pStat ? $pStat['lulus'] : null;
                            $pPct          = $pStat ? $pStat['pct'] : null;
                            $pTotal        = $pStat ? $pStat['total'] : 0;
                        @endphp
                        <tr class="hover:bg-gray-50 transition-colors {{ ($pStat && !$pLulus && $pTotal > 0) ? 'bg-red-50/20' : '' }}">
                            <td class="px-6 py-4 text-xs text-gray-400">{{ $loop->iteration }}</td>
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-3">
                                    <div class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0
                                        {{ $pTotal > 0 ? ($pLulus ? 'bg-green-100' : 'bg-red-100') : 'bg-blue-100' }}">
                                        <span class="text-xs font-semibold
                                            {{ $pTotal > 0 ? ($pLulus ? 'text-green-600' : 'text-red-600') : 'text-blue-600' }}">
                                            {{ strtoupper(substr($participant->name, 0, 1)) }}
                                        </span>
                                    </div>
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">{{ $participant->name }}</div>
                                        @if($isRecorded)
                                        <span class="text-xs text-green-600 font-medium">✓ Sudah dicatat hari ini</span>
                                        @endif
                                    </div>
                                </div>
                                <input type="hidden" name="attendances[{{ $index }}][participant_id]" value="{{ $participant->id }}">
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($pTotal > 0)
                                <div class="flex flex-col items-center space-y-1">
                                    <span class="text-sm font-bold {{ $pLulus ? 'text-green-700' : 'text-red-700' }}">{{ $pPct }}%</span>
                                    <div class="w-14 bg-gray-100 rounded-full h-1.5">
                                        <div class="h-1.5 rounded-full {{ $pLulus ? 'bg-green-500' : 'bg-red-500' }}"
                                             style="width: {{ min($pPct, 100) }}%"></div>
                                    </div>
                                    <span class="text-xs {{ $pLulus ? 'text-green-600' : 'text-red-600' }} font-medium">
                                        {{ $pLulus ? '✓ Memenuhi syarat' : '✗ Belum memenuhi' }}
                                    </span>
                                </div>
                                @else
                                <span class="text-xs text-gray-400">Belum ada data</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center space-x-2">
                                    @foreach([
                                        'present' => ['label' => 'Hadir',     'base' => 'bg-green-100 text-green-700 hover:bg-green-200',   'checked' => 'ring-2 ring-green-400 bg-green-200 text-green-800'],
                                        'late'    => ['label' => 'Terlambat', 'base' => 'bg-orange-100 text-orange-700 hover:bg-orange-200', 'checked' => 'ring-2 ring-orange-400 bg-orange-200 text-orange-800'],
                                        'excused' => ['label' => 'Izin',      'base' => 'bg-yellow-100 text-yellow-700 hover:bg-yellow-200', 'checked' => 'ring-2 ring-yellow-400 bg-yellow-200 text-yellow-800'],
                                        'absent'  => ['label' => 'Absen',     'base' => 'bg-red-100 text-red-700 hover:bg-red-200',          'checked' => 'ring-2 ring-red-400 bg-red-200 text-red-800'],
                                    ] as $val => $meta)
                                    <label class="cursor-pointer">
                                        <input type="radio"
                                               name="attendances[{{ $index }}][status]"
                                               value="{{ $val }}"
                                               {{ $currentStatus === $val ? 'checked' : '' }}
                                               class="sr-only peer">
                                        <span class="text-xs font-medium px-2.5 py-1 rounded-full transition-all
                                            {{ $meta['base'] }} peer-checked:{{ $meta['checked'] }}">
                                            {{ $meta['label'] }}
                                        </span>
                                    </label>
                                    @endforeach
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <input type="text"
                                       name="attendances[{{ $index }}][notes]"
                                       value="{{ $existing?->notes }}"
                                       placeholder="Catatan (opsional)"
                                       class="w-full px-3 py-1.5 text-sm border border-gray-200 rounded-lg focus:ring-1 focus:ring-blue-400 focus:border-blue-400 placeholder-gray-300">
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-400 text-sm">
                                Tidak ada peserta aktif di program ini.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="px-6 py-4 bg-gray-50 border-t flex items-center justify-between">
                <p class="text-xs text-gray-400">Data yang sudah ada akan diperbarui otomatis</p>
                <button type="submit"
                    class="inline-flex items-center px-5 py-2 bg-blue-600 text-white text-sm font-semibold rounded-lg hover:bg-blue-700 transition shadow-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Simpan Kehadiran
                </button>
            </div>
        </form>
    </div>

    <!-- ============================== -->
    <!-- RIWAYAT KEHADIRAN PER TANGGAL -->
    <!-- ============================== -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b bg-gray-50">
            <h3 class="text-base font-semibold text-gray-800">Riwayat Kehadiran</h3>
            <p class="text-xs text-gray-400 mt-0.5">Klik tanggal untuk melihat detail peserta</p>
        </div>

        @forelse($attendanceHistory as $history)
        @php
            $dateKey     = \Carbon\Carbon::parse($history->date)->format('Y-m-d');
            $dayRecords  = \App\Models\Attendance::where('program_id', $program->id)
                ->whereDate('date', $history->date)
                ->with('participant')
                ->get()
                ->keyBy('participant_id');
            $notRecorded = $participants->reject(fn($p) => $dayRecords->has($p->id));
        @endphp
        <div class="border-b last:border-b-0">
            <button type="button"
                    onclick="toggleDay('day-{{ $loop->index }}')"
                    class="w-full px-6 py-4 flex items-center justify-between hover:bg-gray-50 transition-colors text-left">
                <div class="flex items-center gap-3 flex-wrap">
                    <span class="text-sm font-semibold text-gray-800">
                        {{ \Carbon\Carbon::parse($history->date)->locale('id')->translatedFormat('l, d F Y') }}
                    </span>
                    <div class="flex flex-wrap gap-1.5 text-xs">
                        <span class="px-2 py-0.5 bg-green-100 text-green-700 rounded-full font-medium">{{ $history->present }} Hadir</span>
                        <span class="px-2 py-0.5 bg-red-100 text-red-700 rounded-full font-medium">{{ $history->absent }} Absen</span>
                        @if($history->late > 0)<span class="px-2 py-0.5 bg-orange-100 text-orange-700 rounded-full font-medium">{{ $history->late }} Terlambat</span>@endif
                        @if($history->excused > 0)<span class="px-2 py-0.5 bg-yellow-100 text-yellow-700 rounded-full font-medium">{{ $history->excused }} Izin</span>@endif
                        @if($notRecorded->count() > 0)<span class="px-2 py-0.5 bg-gray-200 text-gray-500 rounded-full font-medium">{{ $notRecorded->count() }} Belum dicatat</span>@endif
                    </div>
                </div>
                <div class="flex items-center space-x-2">
                    <span class="text-xs text-gray-400">{{ $history->total }}/{{ $program->participants->count() }}</span>
                    <svg class="w-4 h-4 text-gray-400 transition-transform" id="chevron-day-{{ $loop->index }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </div>
            </button>

            <div id="day-{{ $loop->index }}" class="hidden border-t bg-gray-50">
                <div class="px-6 py-3">
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2">
                        @foreach($participants as $participant)
                        @php
                            $record = $dayRecords->get($participant->id);
                            $status = $record ? $record->status : null;
                            $cfg = match($status) {
                                'present' => ['label' => 'Hadir',         'dot' => 'bg-green-500',  'text' => 'text-green-700',  'border' => 'border-green-200 bg-green-50'],
                                'absent'  => ['label' => 'Tidak Hadir',   'dot' => 'bg-red-500',    'text' => 'text-red-700',    'border' => 'border-red-200 bg-red-50'],
                                'late'    => ['label' => 'Terlambat',     'dot' => 'bg-orange-500', 'text' => 'text-orange-700', 'border' => 'border-orange-200 bg-orange-50'],
                                'excused' => ['label' => 'Izin',          'dot' => 'bg-yellow-500', 'text' => 'text-yellow-700', 'border' => 'border-yellow-200 bg-yellow-50'],
                                default   => ['label' => 'Belum dicatat', 'dot' => 'bg-gray-300',   'text' => 'text-gray-400',   'border' => 'border-gray-200 bg-white'],
                            };
                        @endphp
                        <div class="flex items-center space-x-3 px-3 py-2 rounded-lg border {{ $cfg['border'] }}">
                            <span class="w-2 h-2 rounded-full {{ $cfg['dot'] }} flex-shrink-0"></span>
                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-medium text-gray-800 truncate">{{ $participant->name }}</p>
                                @if($record?->notes)<p class="text-xs text-gray-400 truncate">{{ $record->notes }}</p>@endif
                            </div>
                            <span class="text-xs font-semibold {{ $cfg['text'] }} flex-shrink-0">{{ $cfg['label'] }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="px-6 py-16 text-center">
            <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <p class="text-gray-500 font-medium text-sm">Belum ada riwayat kehadiran</p>
            <p class="text-xs text-gray-400 mt-1">Mulai catat kehadiran hari ini</p>
        </div>
        @endforelse
    </div>
</div>

<script>
function toggleDay(id) {
    const el = document.getElementById(id);
    const chevron = document.getElementById('chevron-' + id);
    el.classList.toggle('hidden');
    chevron.style.transform = el.classList.contains('hidden') ? '' : 'rotate(180deg)';
}
function setAllStatus(status) {
    document.querySelectorAll(`input[type="radio"][value="${status}"]`).forEach(r => r.checked = true);
}
</script>
@endsection