<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Models\MasterProgram;
use App\Models\IndependentCompetencyUnit;
use App\Models\ProgramPelatihanUnits;
use App\Models\Kejuruan;
use App\Models\BidangPelatihan;
use Carbon\Carbon;
use Exception;

class SyncKemnakerPrograms extends Command
{
    protected $signature = 'kemnaker:sync-programs 
                            {--page=1 : Halaman API} 
                            {--limit=20 : Jumlah per halaman}
                            {--max-pages=999 : Maksimal halaman}
                            {--skip-files : Skip download file}
                            {--skip-detail : Skip fetch detail (faster)}
                            {--update-null : Update data yang masih null}
                            {--debug : Mode debug}';

    protected $description = 'Sync program pelatihan dari API Kemnaker dengan detail lengkap';

    private $apiTimeout      = 60;
    private $downloadTimeout = 90;
    private $syncedPrograms  = 0;
    private $updatedPrograms = 0;
    private $syncedUnits     = 0;
    private $failedPrograms  = 0;
    private $filesDownloaded = 0;
    private $filesFailed     = 0;
    private $filesSkipped    = 0;

    private $kejuruanCache = [];
    private $bidangCache   = [];

    /**
     * Mapping dari code API Kemnaker (sub-vocational) → code DB (vocational induk)
     *
     * API Kemnaker kadang pakai code yang lebih spesifik dari yang ada di /vocationals.
     * Mapping ini mentranslate code API → code resmi di tabel kejuruans.
     *
     * Sumber: hasil debug sync + list dari /vocationals API
     */
    private $vocationalCodeMap = [
        // Fashion & Tekstil
        'TBN' => 'FAS',   // Tekstil Busana → Fashion Technology
        'GPL' => 'FAS',   // Garmen Produk → Fashion Technology
        'DBK' => 'IKR',   // Desain Batik → Industri Kreatif
        'PKT' => 'IKR',   // Produk Kulit → Industri Kreatif

        // Sosial & Rumah Tangga
        'ECT' => 'PST',   // Elderly Caretaker → Pelayanan Sosial
        'HSK' => 'PST',   // Housekeeping → Pelayanan Sosial
        'BBS' => 'PST',   // Baby Sitter → Pelayanan Sosial
        'FMC' => 'PST',   // Family Cook → Pelayanan Sosial
        'FDR' => 'TRA',   // Family Driver → Transportasi

        // Pertanian & Pengolahan
        'PRC' => 'TPA',   // Processing → Teknologi Pengolahan Agroindustri
        'MKP' => 'MKP',   // Mekanisasi (sama)

        // Yang sudah sama (tidak perlu map tapi dicantumkan untuk dokumentasi)
        'FAS' => 'FAS',
        'PAR' => 'PAR',
        'KEC' => 'KEC',
        'LAS' => 'LAS',
        'ELK' => 'ELK',
        'TIK' => 'TIK',
        'IKR' => 'IKR',
        'MAR' => 'MAR',
        'BSM' => 'BSM',
        'MAN' => 'MAN',
        'PRD' => 'PRD',
        'TAN' => 'TAN',
        'TER' => 'TER',
        'TRA' => 'TRA',
        'IKN' => 'IKN',
        'PST' => 'PST',
        'KON' => 'KON',
        'OTO' => 'OTO',
        'REF' => 'REF',
        'LIS' => 'LIS',
        'KES' => 'KES',
        'HUT' => 'HUT',
        'TAM' => 'TAM',
        'TPA' => 'TPA',
        'BHS' => 'BHS',
        'JPM' => 'JPM',
    ];

    public function handle()
    {
        $page       = (int) $this->option('page');
        $limit      = (int) $this->option('limit');
        $maxPages   = (int) $this->option('max-pages');
        $skipFiles  = $this->option('skip-files');
        $skipDetail = $this->option('skip-detail');
        $updateNull = $this->option('update-null');
        $debug      = $this->option('debug');

        $this->info('🔄 Memulai sinkronisasi program dari Kemnaker...');
        $this->buildCache($debug);

        if ($updateNull) {
            $this->info('🔧 Mode: Update data yang null');
            $this->updateNullData($skipFiles, $debug);
            return 0;
        }

        if (!$skipFiles) $this->ensureStorageDirectoryExists();

        set_time_limit(0);
        ini_set('memory_limit', '1024M');

        $pageCount = $consecutiveErrors = 0;

        do {
            if ($pageCount >= $maxPages || $consecutiveErrors >= 5) break;

            $url = "https://api.kemnaker.go.id/proglat/v1/public/programs?limit={$limit}&page={$page}&sortBy=created_at&sortOrder=desc";
            $this->info("📄 Halaman {$page}...");

            try {
                $response = Http::withOptions(['verify' => false, 'timeout' => $this->apiTimeout])
                    ->retry(3, 3000)->get($url);

                if ($response->failed()) {
                    $consecutiveErrors++;
                    $page++; $pageCount++;
                    sleep(3);
                    continue;
                }

                $data  = $response->json()['data'] ?? [];
                $count = count($data);

                if ($count === 0) { $this->info("✅ No more data."); break; }

                $consecutiveErrors = 0;
                $bar = $this->output->createProgressBar($count);
                $bar->start();

                foreach ($data as $item) {
                    try {
                        $this->syncProgram($item, $skipFiles, $skipDetail, $debug);
                    } catch (Exception $e) {
                        $this->failedPrograms++;
                        Log::error('Sync failed', ['item_id' => $item['id'] ?? 'no-id', 'error' => $e->getMessage()]);
                    }
                    $bar->advance();
                }

                $bar->finish();
                $this->newLine();
                $page++; $pageCount++;
                sleep(2);

            } catch (Exception $e) {
                $consecutiveErrors++;
                $page++; $pageCount++;
                sleep(5);
            }
        } while (true);

        $this->newLine(2);
        $this->printSummary();
        return 0;
    }

    // =============================================
    // CACHE
    // =============================================

    private function buildCache($debug)
    {
        $this->info('🗂️  Membangun cache dari database...');

        Kejuruan::whereNotNull('code')->where('code', '!=', '')->each(function ($k) {
            $this->kejuruanCache[strtoupper(trim($k->code))] = $k->id;
        });

        BidangPelatihan::all()->each(function ($b) {
            $this->bidangCache[strtoupper(trim($b->bidang_pelatihan))] = $b->id;
        });

        $this->info("   Kejuruan: " . count($this->kejuruanCache) . " | Bidang: " . count($this->bidangCache));

        if ($debug) {
            $this->line("   Kejuruan codes: " . implode(', ', array_keys($this->kejuruanCache)));
        }
    }

    private function ensureStorageDirectoryExists()
    {
        if (!Storage::disk('public')->exists('program-files')) {
            Storage::disk('public')->makeDirectory('program-files');
        }
    }

    // =============================================
    // RESOLVE VOCATIONAL CODE
    // Translate API code → DB code via mapping
    // =============================================

    private function resolveVocationalCode(string $apiCode): string
    {
        $upper = strtoupper(trim($apiCode));
        return $this->vocationalCodeMap[$upper] ?? $upper;
    }

    // =============================================
    // SYNC SATU PROGRAM (full sync mode)
    // =============================================

    private function syncProgram($item, $skipFiles, $skipDetail, $debug)
    {
        $programId = $item['id'] ?? null;
        $code      = $item['code'] ?? null;

        if (!$programId) throw new Exception('ID hilang');
        if (empty($code)) {
            $code = 'AUTO-' . substr(md5(($item['title'] ?? '') . $programId), 0, 12);
        }

        if (!$skipDetail) {
            $detail = $this->fetchDetailById($programId, $debug);
            if ($detail) $item = array_merge($item, $detail);
        }

        [$kejuruanId, $bidangId] = $this->resolveKejuruanBidang($item, $code, $debug);

        $tanggal = null;
        foreach (['effective_date', 'created_at'] as $f) {
            if (!empty($item[$f])) {
                try { $tanggal = Carbon::parse($item[$f]); break; } catch (Exception $e) {}
            }
        }

        $existing = MasterProgram::where('code', $code)->first();
        $isNew    = !$existing;

        $data = [
            'name'                => $item['title'] ?? 'Unknown',
            'program_pelatihan'   => $item['title'] ?? 'Unknown',
            'description'         => $item['description'] ?? null,
            'duration_hours'      => (int)($item['total_duration'] ?? $item['total_topic_duration'] ?? 0),
            'kejuruan_id'         => $kejuruanId,
            'bidang_pelatihan_id' => $bidangId,
            'versi'               => $item['version'] ?? 1,
            'tanggal'             => $tanggal,
            'is_active'           => ($item['status'] ?? '') === 'approved',
            'updated_by'          => 1,
        ];

        if ($isNew) $data['created_by'] = 1;
        if ($existing && $existing->file_program) {
            $data['file_program'] = $existing->file_program;
        }

        $master = MasterProgram::updateOrCreate(['code' => $code], $data);

        if ($isNew) $this->syncedPrograms++;
        elseif ($master->wasChanged()) $this->updatedPrograms++;

        if (!$skipFiles) {
            $fileUri = $item['download_material_uri'] ?? $item['material_value'] ?? null;
            if ($fileUri) {
                $result = $this->downloadFile($programId, $fileUri, $master, $debug);
                if ($result === 'success')     $this->filesDownloaded++;
                elseif ($result === 'skipped') $this->filesSkipped++;
                else                           $this->filesFailed++;
            }
        }

        if (!empty($item['program_topics'])) {
            foreach ($item['program_topics'] as $topic) {
                $tCode = $topic['code'] ?? null;
                if (empty($tCode) || $tCode === '-' || strlen($tCode) < 3) continue;
                try {
                    $unit = IndependentCompetencyUnit::updateOrCreate(
                        ['code' => $tCode],
                        ['name' => $topic['title'] ?? 'Unknown', 'description' => $topic['description'] ?? null]
                    );
                    ProgramPelatihanUnits::updateOrCreate(
                        ['master_programs_id' => $master->id, 'independent_competency_units_id' => $unit->id],
                        ['program_pelatihan_id' => $master->id, 'type_unit' => $topic['type'] ?? 'skkni',
                         'jp' => $topic['duration'] ?? 0, 'sub_unit_kompetensi' => 'N',
                         'name' => $topic['title'] ?? $unit->name]
                    );
                    $this->syncedUnits++;
                } catch (Exception $e) { /* silent */ }
            }
        }
    }

    // =============================================
    // UPDATE NULL DATA
    // =============================================

    private function updateNullData($skipFiles, $debug)
    {
        $this->info('🔍 Mencari program dengan data null...');

        $nullPrograms = MasterProgram::where(function ($q) {
            $q->whereNull('kejuruan_id')->orWhereNull('bidang_pelatihan_id');
        })->get();

        $total = $nullPrograms->count();
        $this->info("   Ditemukan {$total} program dengan data null");

        if ($total === 0) {
            $this->info('✅ Semua data sudah lengkap!');
            return;
        }

        $bar      = $this->output->createProgressBar($total);
        $notFound = 0;
        $bar->start();

        foreach ($nullPrograms as $program) {
            try {
                $kemnakerItem = $this->searchByKeyword($program->name, $program->code, $debug);

                if (!$kemnakerItem) {
                    $notFound++;
                    if ($debug) $this->warn("\n   ⚠ Tidak ditemukan: [{$program->code}] {$program->name}");
                    $bar->advance();
                    continue;
                }

                $kemnakerProgramId = $kemnakerItem['id'] ?? null;
                $detail = $kemnakerProgramId ? $this->fetchDetailById($kemnakerProgramId, $debug) : null;
                $item   = $detail ? array_merge($kemnakerItem, $detail) : $kemnakerItem;

                if ($debug) {
                    $apiVocCode  = isset($item['vocational']['code'])     ? $item['vocational']['code']     : '-';
                    $dbVocCode   = $this->resolveVocationalCode($apiVocCode);
                    $subName     = isset($item['sub_vocational']['name']) ? $item['sub_vocational']['name'] : '-';
                    $this->line("\n   📋 [{$program->code}] voc_api:[{$apiVocCode}] → voc_db:[{$dbVocCode}] sub:[{$subName}]");
                }

                [$kejuruanId, $bidangId] = $this->resolveKejuruanBidang($item, $program->code, $debug);

                $updateData = [];
                if (is_null($program->kejuruan_id) && $kejuruanId) {
                    $updateData['kejuruan_id'] = $kejuruanId;
                }
                if (is_null($program->bidang_pelatihan_id) && $bidangId) {
                    $updateData['bidang_pelatihan_id'] = $bidangId;
                }
                if (is_null($program->created_by)) $updateData['created_by'] = 1;
                if (is_null($program->updated_by))  $updateData['updated_by']  = 1;

                if (!empty($updateData)) {
                    $program->update($updateData);
                    $this->updatedPrograms++;
                    if ($debug) {
                        $keys = implode(', ', array_keys($updateData));
                        $this->line("\n   💾 Updated [{$program->code}]: {$keys}");
                    }
                }

                usleep(300000); // 0.3 detik jeda

            } catch (Exception $e) {
                if ($debug) $this->error("\n   Error [{$program->code}]: " . $e->getMessage());
                Log::error('Update null failed', ['code' => $program->code, 'error' => $e->getMessage()]);
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();

        if ($notFound > 0) {
            $this->warn("   {$notFound} program tidak ditemukan di API Kemnaker");
        }

        $this->info('✅ Update data null selesai!');
        $this->printSummary();
    }

    // =============================================
    // HELPER: Search by keyword
    // =============================================

    private function searchByKeyword(string $name, string $code, bool $debug): ?array
    {
        $keyword = trim($name);

        try {
            $response = Http::withOptions(['verify' => false, 'timeout' => 20])
                ->retry(2, 2000)
                ->get('https://api.kemnaker.go.id/proglat/v1/public/programs', [
                    'limit'   => 5,
                    'keyword' => $keyword,
                ]);

            if (!$response->successful()) return null;

            $results = $response->json()['data'] ?? [];
            if (empty($results)) return null;

            $normalizedKeyword = strtolower(trim($keyword));

            // 1. Exact title match
            foreach ($results as $item) {
                if (strtolower(trim($item['title'] ?? '')) === $normalizedKeyword) {
                    return $item;
                }
            }

            // 2. Exact code match
            foreach ($results as $item) {
                if (strtoupper(trim($item['code'] ?? '')) === strtoupper($code)) {
                    return $item;
                }
            }

            // 3. Similarity ≥ 85%
            $bestScore = 0;
            $bestItem  = null;
            foreach ($results as $item) {
                similar_text($normalizedKeyword, strtolower(trim($item['title'] ?? '')), $percent);
                if ($percent > $bestScore) {
                    $bestScore = $percent;
                    $bestItem  = $item;
                }
            }

            if ($bestScore >= 85 && $bestItem) return $bestItem;

        } catch (Exception $e) {
            Log::warning('searchByKeyword failed', ['code' => $code, 'error' => $e->getMessage()]);
        }

        return null;
    }

    // =============================================
    // HELPER: Fetch detail by ID
    // =============================================

    private function fetchDetailById(string $id, bool $debug): ?array
    {
        try {
            $response = Http::withOptions(['verify' => false, 'timeout' => 30])
                ->retry(2, 2000)
                ->get("https://api.kemnaker.go.id/proglat/v1/public/programs/{$id}");

            if ($response->successful() && isset($response->json()['data'])) {
                return $response->json()['data'];
            }
        } catch (Exception $e) {
            if ($debug) $this->warn("\n   Detail gagal ({$id}): " . $e->getMessage());
        }

        return null;
    }

    // =============================================
    // HELPER: Extract kejuruan code dari kode program
    //
    // Format kode program Kemnaker:
    //   P.85.BHS03.2643.K1.24.L.0640.01.01
    //         ^^^
    //   Bagian ke-3 setelah split titik = kode kejuruan (3 huruf awal)
    //   Contoh: BHS03 → BHS, FAS02 → FAS, TIK17 → TIK
    // =============================================

    private function extractVocCodeFromProgramCode(string $programCode): string
    {
        // Hanya berlaku untuk kode format standar Kemnaker (mengandung titik)
        if (!str_contains($programCode, '.')) return '';

        $parts = explode('.', $programCode);
        // Index 2 = bagian ketiga, contoh: BHS03, FAS02, TIK17
        $segment = strtoupper($parts[2] ?? '');

        // Ambil 3 huruf pertama (huruf saja, bukan angka)
        if (preg_match('/^([A-Z]{2,4})/', $segment, $m)) {
            return $m[1];
        }

        return '';
    }

    // =============================================
    // HELPER: Resolve kejuruan & bidang
    // ✅ Gunakan vocationalCodeMap untuk translate API code → DB code
    // ✅ Fallback: extract dari kode program jika vocational kosong
    // =============================================

    private function resolveKejuruanBidang(array $item, string $code, bool $debug): array
    {
        $kejuruanId = null;
        $bidangId   = null;

        // Ambil code dari API
        $apiVocCode = strtoupper(trim(
            isset($item['vocational']['code']) ? $item['vocational']['code'] : ''
        ));

        // Fallback: jika API tidak punya vocational code, extract dari kode program
        if (empty($apiVocCode) || $apiVocCode === '-') {
            $extracted = $this->extractVocCodeFromProgramCode($code);
            if (!empty($extracted)) {
                $apiVocCode = $extracted;
                if ($debug) {
                    $this->line("\n   🔍 Fallback kode dari program: [{$code}] → [{$apiVocCode}]");
                }
            }
        }

        if (!empty($apiVocCode) && $apiVocCode !== '-') {
            // Translate API code → DB code
            $dbVocCode  = $this->resolveVocationalCode($apiVocCode);
            $kejuruanId = $this->kejuruanCache[$dbVocCode] ?? null;

            if (!$kejuruanId && $debug) {
                $this->warn("\n   ⚠ Kejuruan [{$apiVocCode}→{$dbVocCode}] tidak ada di DB ({$code})");
            }
        }

        // Bidang: sub_vocational.name — exact dulu, lalu partial
        $subName = strtoupper(trim(
            isset($item['sub_vocational']['name'])
                ? $item['sub_vocational']['name']
                : (isset($item['sub_vocational']['title']) ? $item['sub_vocational']['title'] : '')
        ));

        if (!empty($subName) && $subName !== '-') {
            // Exact
            if (isset($this->bidangCache[$subName])) {
                $bidangId = $this->bidangCache[$subName];
            }

            // Partial
            if (!$bidangId) {
                foreach ($this->bidangCache as $bidangName => $bid) {
                    if (str_contains($bidangName, $subName) || str_contains($subName, $bidangName)) {
                        $bidangId = $bid;
                        if ($debug) $this->line("\n   ~ Bidang partial: [{$subName}] → [{$bidangName}]");
                        break;
                    }
                }
            }

            if (!$bidangId && $debug) {
                $this->warn("\n   ⚠ Bidang [{$subName}] tidak ada di DB ({$code})");
            }
        }

        return [$kejuruanId, $bidangId];
    }

    // =============================================
    // DOWNLOAD FILE
    // =============================================

    private function downloadFile($programId, $fileUri, $master, $debug)
    {
        try {
            $extension = pathinfo($fileUri, PATHINFO_EXTENSION) ?: 'pdf';
            $safeCode  = preg_replace('/[^A-Za-z0-9\-]/', '-', $master->code);
            $localPath = "program-files/{$safeCode}.{$extension}";

            if ($master->file_program && Storage::disk('public')->exists($master->file_program)) {
                return 'skipped';
            }

            $fullUrl  = strpos($fileUri, 'http') === 0 ? $fileUri : "https://proglat-assets.kemnaker.go.id/{$fileUri}";
            $response = Http::withOptions(['verify' => false, 'timeout' => $this->downloadTimeout])->get($fullUrl);

            if (!$response->successful() || strlen($response->body()) < 500) return 'failed';

            Storage::disk('public')->put($localPath, $response->body());
            if (Storage::disk('public')->exists($localPath)) {
                $master->update(['file_program' => $localPath]);
                return 'success';
            }
            return 'failed';

        } catch (Exception $e) {
            return 'failed';
        }
    }

    // =============================================
    // SUMMARY
    // =============================================

    private function printSummary()
    {
        $this->table(['Status', 'Jumlah'], [
            ['✨ Program baru',     $this->syncedPrograms],
            ['🔄 Program diupdate', $this->updatedPrograms],
            ['📦 Unit tersimpan',   $this->syncedUnits],
            ['❌ Program gagal',    $this->failedPrograms],
            ['📥 File downloaded',  $this->filesDownloaded],
            ['⏭️ File skipped',    $this->filesSkipped],
            ['❌ File failed',      $this->filesFailed],
        ]);
    }
}