@extends('layouts.app')

@section('title', 'Kehadiran - ' . ($program->masterProgram->name ?? 'Program'))

@section('content')
<div class="space-y-6">

    <!-- Header -->
    <div class="flex items-center justify-between">
        <a href="{{ route('admin.attendance.index') }}"
           class="inline-flex items-center text-sm text-gray-500 hover:text-gray-800 transition-colors">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Kembali
        </a>
        <a href="{{ route('admin.attendance.recap') }}"
           class="inline-flex items-center px-4 py-2 bg-purple-600 text-white text-sm font-medium rounded-lg hover:bg-purple-700 transition shadow-sm">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
            </svg>
            Rekap Keseluruhan
        </a>
    </div>

    <!-- Flash Messages -->
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

    <!-- Program Info -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
        <div class="flex items-start justify-between">
            <div>
                <h2 class="text-xl font-bold text-gray-900">{{ $program->masterProgram->name ?? 'N/A' }}</h2>
                <p class="text-gray-500 text-sm mt-1">
                    {{ $program->batch }} &nbsp;·&nbsp;
                    {{ $program->start_date->format('d M Y') }} – {{ $program->end_date->format('d M Y') }}
                </p>
            </div>
            <div class="text-right">
                <div class="text-xs text-gray-400 uppercase tracking-wide">Total Peserta</div>
                <div class="text-3xl font-bold text-gray-900">{{ $program->participants->count() }}</div>
            </div>
        </div>
    </div>

    <!-- ============================================ -->
    <!-- AT-RISK SUMMARY BANNER -->
    <!-- ============================================ -->
    @php
        $minAttendance = 80;
        $allStats = $program->participants->map(function($participant) use ($program, $minAttendance) {
            $records      = \App\Models\Attendance::where('program_id', $program->id)
                ->where('participant_id', $participant->id)->get();
            $total        = $records->count();
            $present      = $records->where('status', 'present')->count();
            $absent       = $records->where('status', 'absent')->count();
            $pct          = $total > 0 ? round(($present / $total) * 100) : 0;
            $lulus        = $pct >= $minAttendance;
            $maxAbsen     = (int) floor($total * (1 - $minAttendance / 100));
            $sisaBolehAbsen = max(0, $maxAbsen - $absent);
            return compact('participant', 'total', 'present', 'absent', 'pct', 'lulus', 'sisaBolehAbsen');
        });
        $atRisk  = $allStats->filter(fn($s) => !$s['lulus'] && $s['total'] > 0);
        $lulusAll = $allStats->filter(fn($s) => $s['lulus']);
    @endphp

    @if($atRisk->count() > 0)
    <div class="bg-red-50 border border-red-200 rounded-xl overflow-hidden">
        <div class="px-5 py-4 border-b border-red-200 flex items-center space-x-3">
            <svg class="w-5 h-5 text-red-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            <div>
                <p class="text-sm font-bold text-red-800">
                    {{ $atRisk->count() }} peserta belum memenuhi syarat kehadiran (min. {{ $minAttendance }}%)
                </p>
                <p class="text-xs text-red-600 mt-0.5">Peserta berikut perlu diperhatikan kehadirannya</p>
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
    @else
    @if($allStats->filter(fn($s) => $s['total'] > 0)->count() > 0)
    <div class="bg-green-50 border border-green-200 rounded-xl px-5 py-4 flex items-center space-x-3">
        <svg class="w-5 h-5 text-green-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <p class="text-sm font-semibold text-green-800">
            Semua peserta sudah memenuhi syarat kehadiran minimum {{ $minAttendance }}%
        </p>
    </div>
    @endif
    @endif

    <!-- ============================================ -->
    <!-- FORM ABSENSI DENGAN KALENDER -->
    <!-- ============================================ -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b bg-gray-50 flex items-center justify-between">
            <div>
                <h3 class="text-base font-semibold text-gray-800">Catat Kehadiran</h3>
                <p class="text-xs text-gray-400 mt-0.5">Admin dapat mencatat atau memperbarui kehadiran untuk tanggal manapun</p>
            </div>
            @if($existingAttendances->count() > 0)
            <span class="px-3 py-1 bg-blue-100 text-blue-700 text-xs font-semibold rounded-full">
                {{ $existingAttendances->count() }} peserta sudah tercatat
            </span>
            @endif
        </div>

        <div class="p-6">
            <!-- Date picker -->
            <div class="mb-5">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Tanggal Absensi</label>
                <div class="flex items-center flex-wrap gap-3">
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <input type="date"
                               id="attendance-date-picker"
                               value="{{ $selectedDate->format('Y-m-d') }}"
                               max="{{ now()->format('Y-m-d') }}"
                               class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm text-gray-800 font-medium">
                    </div>
                    <button type="button" onclick="loadDateAttendance()"
                        class="px-4 py-2 bg-gray-800 text-white text-sm font-medium rounded-lg hover:bg-gray-900 transition">
                        Muat Data
                    </button>
                    <span class="text-sm text-gray-600 font-medium" id="date-label">
                        {{ $selectedDate->locale('id')->translatedFormat('l, d F Y') }}
                    </span>
                </div>
                <p class="mt-1.5 text-xs text-gray-400">Jika data sudah ada untuk tanggal ini, form otomatis terisi.</p>
            </div>

            <form action="{{ route('admin.attendance.record') }}" method="POST" id="attendance-form">
                @csrf
                <input type="hidden" name="program_id" value="{{ $program->id }}">
                <input type="hidden" name="date" id="form-date" value="{{ $selectedDate->format('Y-m-d') }}">

                @if($program->participants->count() > 0)
                <!-- Quick actions -->
                <div class="flex items-center space-x-3 mb-3">
                    <span class="text-xs text-gray-500 font-medium">Tandai semua:</span>
                    <button type="button" onclick="setAllStatus('present')"
                        class="px-3 py-1 text-xs bg-green-100 text-green-700 rounded-md hover:bg-green-200 font-medium transition">✓ Hadir Semua</button>
                    <button type="button" onclick="setAllStatus('absent')"
                        class="px-3 py-1 text-xs bg-red-100 text-red-700 rounded-md hover:bg-red-200 font-medium transition">✗ Absen Semua</button>
                </div>

                <div class="rounded-lg border border-gray-200 overflow-hidden">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-gray-50 border-b text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                <th class="px-4 py-3 text-left w-8">#</th>
                                <th class="px-4 py-3 text-left">Peserta</th>
                                <th class="px-4 py-3 text-center">Kehadiran S/D Hari Ini</th>
                                <th class="px-4 py-3 text-center">Status Hari Ini</th>
                                <th class="px-4 py-3 text-left">Catatan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($program->participants as $index => $participant)
                            @php
                                $existing      = $existingAttendances->get($participant->id);
                                $currentStatus = $existing ? $existing->status : 'present';
                                $pStat         = $allStats->firstWhere('participant.id', $participant->id);
                                $pLulus        = $pStat ? $pStat['lulus'] : null;
                                $pPct          = $pStat ? $pStat['pct'] : null;
                                $pTotal        = $pStat ? $pStat['total'] : 0;
                            @endphp
                            <tr class="hover:bg-gray-50 transition-colors {{ ($pStat && !$pLulus && $pTotal > 0) ? 'bg-red-50/20' : '' }}">
                                <td class="px-4 py-3 text-xs text-gray-400">{{ $index + 1 }}</td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0
                                            {{ $pTotal > 0 ? ($pLulus ? 'bg-green-100' : 'bg-red-100') : 'bg-gray-100' }}">
                                            <span class="text-xs font-bold
                                                {{ $pTotal > 0 ? ($pLulus ? 'text-green-700' : 'text-red-700') : 'text-gray-500' }}">
                                                {{ strtoupper(substr($participant->name, 0, 1)) }}
                                            </span>
                                        </div>
                                        <div>
                                            <p class="font-medium text-gray-900">{{ $participant->name }}</p>
                                            @if($participant->nik)
                                            <p class="text-xs text-gray-400">{{ $participant->nik }}</p>
                                            @endif
                                        </div>
                                    </div>
                                    <input type="hidden" name="attendances[{{ $index }}][participant_id]" value="{{ $participant->id }}">
                                </td>
                                <td class="px-4 py-3 text-center">
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
                                <td class="px-4 py-3">
                                    <div class="flex items-center justify-center flex-wrap gap-1.5">
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
                                <td class="px-4 py-3">
                                    <input type="text"
                                           name="attendances[{{ $index }}][notes]"
                                           value="{{ $existing?->notes }}"
                                           placeholder="Catatan (opsional)"
                                           class="w-full px-3 py-1.5 text-sm border border-gray-200 rounded-lg focus:ring-1 focus:ring-blue-400 focus:border-blue-400 placeholder-gray-300">
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-4 flex items-center justify-between">
                    <p class="text-xs text-gray-400">Data yang sudah ada akan diperbarui otomatis</p>
                    <button type="submit"
                        class="inline-flex items-center px-5 py-2 bg-blue-600 text-white text-sm font-semibold rounded-lg hover:bg-blue-700 transition shadow-sm">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Simpan Kehadiran
                    </button>
                </div>
                @else
                <div class="text-center py-8 text-gray-400 text-sm">Belum ada peserta terdaftar</div>
                @endif
            </form>
        </div>
    </div>

    <!-- ============================================ -->
    <!-- RIWAYAT KEHADIRAN (dengan detail peserta) -->
    <!-- ============================================ -->
    @if($dates->count() > 0)
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b bg-gray-50">
            <h3 class="text-base font-semibold text-gray-800">Riwayat Kehadiran</h3>
            <p class="text-xs text-gray-400 mt-0.5">Klik tanggal untuk expand detail peserta, atau klik "Edit" untuk loncat ke form</p>
        </div>

        @foreach($dates as $date)
        @php
            $dateKey      = $date instanceof \Carbon\Carbon ? $date->format('Y-m-d') : $date;
            $dayAtt       = $attendances[$dateKey] ?? collect();
            $presentCount = $dayAtt->where('status', 'present')->count();
            $absentCount  = $dayAtt->where('status', 'absent')->count();
            $excusedCount = $dayAtt->where('status', 'excused')->count();
            $lateCount    = $dayAtt->where('status', 'late')->count();
            $isSelected   = $dateKey === $selectedDate->format('Y-m-d');
            $recordedIds  = $dayAtt->pluck('participant_id')->toArray();
            $notRecorded  = $program->participants->reject(fn($p) => in_array($p->id, $recordedIds));
        @endphp
        <div class="border-b last:border-b-0 {{ $isSelected ? 'bg-blue-50' : '' }}">
            <button type="button"
                    onclick="toggleHistory('hist-{{ $loop->index }}')"
                    class="w-full px-6 py-4 flex items-center justify-between hover:bg-gray-50 transition-colors text-left {{ $isSelected ? 'hover:bg-blue-100' : '' }}">
                <div class="flex items-center gap-3 flex-wrap">
                    <div class="flex items-center space-x-2">
                        @if($isSelected)<span class="w-2 h-2 rounded-full bg-blue-500"></span>@endif
                        <span class="text-sm font-semibold text-gray-800">
                            {{ \Carbon\Carbon::parse($date)->locale('id')->translatedFormat('l, d F Y') }}
                        </span>
                        @if($isSelected)
                        <span class="px-2 py-0.5 bg-blue-100 text-blue-700 text-xs rounded-full font-medium">Aktif</span>
                        @endif
                    </div>
                    <div class="flex flex-wrap gap-1.5 text-xs">
                        <span class="px-2 py-0.5 bg-green-100 text-green-700 rounded-full font-medium">{{ $presentCount }} Hadir</span>
                        <span class="px-2 py-0.5 bg-red-100 text-red-700 rounded-full font-medium">{{ $absentCount }} Absen</span>
                        @if($lateCount > 0)<span class="px-2 py-0.5 bg-orange-100 text-orange-700 rounded-full font-medium">{{ $lateCount }} Terlambat</span>@endif
                        @if($excusedCount > 0)<span class="px-2 py-0.5 bg-yellow-100 text-yellow-700 rounded-full font-medium">{{ $excusedCount }} Izin</span>@endif
                        @if($notRecorded->count() > 0)<span class="px-2 py-0.5 bg-gray-200 text-gray-500 rounded-full font-medium">{{ $notRecorded->count() }} Belum dicatat</span>@endif
                    </div>
                </div>
                <div class="flex items-center space-x-3 flex-shrink-0">
                    <button type="button"
                            onclick="event.stopPropagation(); jumpToDate('{{ $dateKey }}')"
                            class="text-xs text-blue-600 hover:text-blue-800 font-medium px-2 py-1 hover:bg-blue-100 rounded transition">
                        Edit
                    </button>
                    <svg class="w-4 h-4 text-gray-400 transition-transform" id="chevron-hist-{{ $loop->index }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </div>
            </button>

            <!-- Detail peserta -->
            <div id="hist-{{ $loop->index }}" class="hidden border-t">
                <div class="px-6 py-4 bg-gray-50">
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2">
                        @foreach($program->participants as $participant)
                        @php
                            $record = $dayAtt->firstWhere('participant_id', $participant->id);
                            $status = $record ? $record->status : null;
                            $cfg = match($status) {
                                'present' => ['label' => 'Hadir',          'dot' => 'bg-green-500',  'text' => 'text-green-700',  'border' => 'border-green-200 bg-green-50'],
                                'absent'  => ['label' => 'Tidak Hadir',    'dot' => 'bg-red-500',    'text' => 'text-red-700',    'border' => 'border-red-200 bg-red-50'],
                                'late'    => ['label' => 'Terlambat',      'dot' => 'bg-orange-500', 'text' => 'text-orange-700', 'border' => 'border-orange-200 bg-orange-50'],
                                'excused' => ['label' => 'Izin',           'dot' => 'bg-yellow-500', 'text' => 'text-yellow-700', 'border' => 'border-yellow-200 bg-yellow-50'],
                                default   => ['label' => 'Belum dicatat',  'dot' => 'bg-gray-300',   'text' => 'text-gray-400',   'border' => 'border-gray-200 bg-white'],
                            };
                        @endphp
                        <div class="flex items-center space-x-3 px-3 py-2 rounded-lg border {{ $cfg['border'] }}">
                            <span class="w-2 h-2 rounded-full {{ $cfg['dot'] }} flex-shrink-0"></span>
                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-medium text-gray-800 truncate">{{ $participant->name }}</p>
                                @if($record?->notes)
                                <p class="text-xs text-gray-400 truncate">{{ $record->notes }}</p>
                                @endif
                            </div>
                            <span class="text-xs font-semibold {{ $cfg['text'] }} flex-shrink-0">{{ $cfg['label'] }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @else
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-12 text-center">
        <svg class="w-14 h-14 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
        </svg>
        <p class="text-gray-400 font-medium">Belum ada riwayat kehadiran</p>
        <p class="text-xs text-gray-400 mt-1">Gunakan form di atas untuk mulai mencatat</p>
    </div>
    @endif
</div>

<script>
document.getElementById('attendance-date-picker').addEventListener('change', function() {
    const date = new Date(this.value + 'T00:00:00');
    document.getElementById('date-label').textContent = date.toLocaleDateString('id-ID', {
        weekday: 'long', year: 'numeric', month: 'long', day: 'numeric'
    });
});
function loadDateAttendance() {
    const date = document.getElementById('attendance-date-picker').value;
    if (!date) return;
    window.location.href = '{{ route("admin.attendance.show", $program) }}?date=' + date;
}
function jumpToDate(dateStr) {
    document.getElementById('attendance-date-picker').value = dateStr;
    window.location.href = '{{ route("admin.attendance.show", $program) }}?date=' + dateStr;
}
function setAllStatus(status) {
    document.querySelectorAll(`input[type="radio"][value="${status}"]`).forEach(r => r.checked = true);
}
function toggleHistory(id) {
    const el = document.getElementById(id);
    const chevron = document.getElementById('chevron-' + id);
    el.classList.toggle('hidden');
    chevron.style.transform = el.classList.contains('hidden') ? '' : 'rotate(180deg)';
}
document.getElementById('attendance-form').addEventListener('submit', function() {
    document.getElementById('form-date').value = document.getElementById('attendance-date-picker').value;
});
document.getElementById('attendance-date-picker').addEventListener('keydown', function(e) {
    if (e.key === 'Enter') { e.preventDefault(); loadDateAttendance(); }
});
</script>
@endsection