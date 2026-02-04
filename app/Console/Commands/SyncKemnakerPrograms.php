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
use Illuminate\Support\Str;

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

    private $apiTimeout = 180;
    private $downloadTimeout = 90;
    private $syncedPrograms = 0;
    private $updatedPrograms = 0;
    private $syncedUnits = 0;
    private $failedPrograms = 0;
    private $filesDownloaded = 0;
    private $filesFailed = 0;
    private $filesSkipped = 0;

    public function handle()
    {
        $page = (int) $this->option('page');
        $limit = (int) $this->option('limit');
        $maxPages = (int) $this->option('max-pages');
        $skipFiles = $this->option('skip-files');
        $skipDetail = $this->option('skip-detail');
        $updateNull = $this->option('update-null');
        $debug = $this->option('debug');

        $this->info('🔄 Memulai sinkronisasi program dari Kemnaker...');
        
        // Mode update data null
        if ($updateNull) {
            $this->info('🔧 Mode: Update data yang null');
            $this->updateNullData($skipFiles, $debug);
            return 0;
        }
        
        if ($skipDetail) $this->warn('⚠️ Mode: Basic sync (tanpa detail)');
        else $this->info('📋 Mode: Full sync dengan detail');

        if (!$skipFiles) {
            $this->info('📥 Download file: Aktif');
            $this->ensureStorageDirectoryExists();
        } else {
            $this->warn('⏭️ Download file: Dilewati');
        }

        set_time_limit(0);
        ini_set('memory_limit', '1024M');

        $pageCount = 0;
        $consecutiveErrors = 0;

        do {
            if ($pageCount >= $maxPages || $consecutiveErrors >= 5) {
                if ($consecutiveErrors >= 5) $this->error('Terlalu banyak error berturut-turut.');
                break;
            }

            $url = "https://api.kemnaker.go.id/proglat/v1/public/programs?limit={$limit}&page={$page}&hasCode=false&sortBy=created_at&sortOrder=desc";
            
            $this->info("📄 Halaman {$page}...");

            try {
                $response = Http::withOptions([
                    'verify' => false,
                    'timeout' => $this->apiTimeout,
                    'connect_timeout' => 60,
                ])->retry(5, 5000)->get($url);

                if ($response->failed()) {
                    $status = $response->status();
                    $this->warn("⏰ HTTP {$status} halaman {$page}");
                    Log::warning('Kemnaker HTTP fail', ['page' => $page, 'status' => $status]);
                    $consecutiveErrors++;
                    $page++;
                    $pageCount++;
                    sleep(5);
                    continue;
                }

                $json = $response->json();

                if (!is_array($json) || !isset($json['data']) || !is_array($json['data'])) {
                    $this->error("❌ Response tidak valid (no 'data' array)");
                    Log::error('Invalid API format', ['page' => $page, 'json' => $json]);
                    break;
                }

                $data = $json['data'];
                $count = count($data);

                if ($count === 0) {
                    $this->info("✅ No more data. Selesai.");
                    break;
                }

                $this->info("   {$count} program ditemukan");

                $consecutiveErrors = 0;

                $bar = $this->output->createProgressBar($count);
                $bar->start();

                foreach ($data as $item) {
                    try {
                        $this->syncProgram($item, $skipFiles, $skipDetail, $debug);
                    } catch (Exception $e) {
                        $this->failedPrograms++;
                        $fallbackCode = $item['id'] ?? 'unknown-' . Str::random(6);
                        $this->error("   Gagal: {$fallbackCode} - " . $e->getMessage());
                        if ($debug) {
                            $this->line("      File: {$e->getFile()} Line: {$e->getLine()}");
                        }
                        Log::error('Sync failed', ['item_id' => $item['id'] ?? 'no-id', 'error' => $e->getMessage()]);
                    }
                    $bar->advance();
                }

                $bar->finish();
                $this->newLine();

                $page++;
                $pageCount++;
                sleep(2);

            } catch (Exception $e) {
                $this->error("❌ Halaman {$page} error: " . $e->getMessage());
                $consecutiveErrors++;
                $page++;
                $pageCount++;
                sleep(5);
            }
        } while (true);

        $this->newLine(2);
        $this->info("✅ Sinkronisasi selesai!");
        $this->table(
            ['Status', 'Jumlah'],
            [
                ['✨ Program baru', $this->syncedPrograms],
                ['🔄 Program diupdate', $this->updatedPrograms],
                ['📦 Unit tersimpan', $this->syncedUnits],
                ['❌ Program gagal', $this->failedPrograms],
                ['📥 File downloaded', $this->filesDownloaded],
                ['⏭️ File skipped', $this->filesSkipped],
                ['❌ File failed', $this->filesFailed],
            ]
        );

        return 0;
    }

    private function ensureStorageDirectoryExists()
    {
        if (!Storage::disk('public')->exists('program-files')) {
            Storage::disk('public')->makeDirectory('program-files');
        }
    }

    private function syncProgram($item, $skipFiles, $skipDetail, $debug)
    {
        $programId = $item['id'] ?? null;
        $code = $item['code'] ?? null;

        if (!$programId) {
            throw new Exception('ID program hilang sama sekali');
        }

        // Jika code kosong → generate fallback unik berdasarkan ID + title hash
        if (empty($code)) {
            $titleHash = substr(md5($item['title'] ?? 'no-title' . $programId), 0, 12);
            $code = 'AUTO-' . $titleHash;
            if ($debug) $this->warn("   Code kosong → generate fallback: {$code}");
        }

        // Fetch detail jika tidak skip
        if (!$skipDetail) {
            try {
                $detailUrl = "https://api.kemnaker.go.id/proglat/v1/public/programs/{$programId}";
                $detailResponse = Http::withOptions(['verify' => false, 'timeout' => 30])->get($detailUrl);

                if ($detailResponse->successful() && isset($detailResponse->json()['data'])) {
                    $item = array_merge($item, $detailResponse->json()['data']);
                    if ($debug) $this->line("   🔍 Detail diambil untuk {$code}");
                }
            } catch (Exception $e) {
                if ($debug) $this->warn("   Detail fetch gagal: " . $e->getMessage());
            }
        }

        // === AMBIL KEJURUAN DARI DATABASE BERDASARKAN CODE ===
        $kejuruanId = null;
        $vocCode = $item['vocational']['code'] ?? null;
        
        if (!empty($vocCode)) {
            // Cari di database berdasarkan code
            $kejuruan = Kejuruan::where('code', $vocCode)->first();
            
            if ($kejuruan) {
                $kejuruanId = $kejuruan->id;
                if ($debug) $this->line("   ✓ Kejuruan ditemukan: {$kejuruan->kejuruan} (Code: {$vocCode})");
            } else {
                if ($debug) $this->warn("   ⚠ Kejuruan dengan code {$vocCode} tidak ditemukan di database. Harap sync kejuruan terlebih dahulu.");
                Log::warning('Kejuruan not found in database', ['code' => $vocCode, 'program' => $code]);
            }
        }

        // === AMBIL BIDANG DARI DATABASE BERDASARKAN CODE ===
        $bidangId = null;
        // $subCode = $item['sub_vocational']['code'] ?? null;
        
        // if (!empty($subCode)) {
        //     // Cari di database berdasarkan code
        //     // $bidang = BidangPelatihan::where('code', $subCode)->first();
            
        //     if ($bidang) {
        //         $bidangId = $bidang->id;
        //         if ($debug) $this->line("   ✓ Bidang ditemukan: {$bidang->bidang_pelatihan} (id: {$subCode})");
        //     } else {
        //         if ($debug) $this->warn("   ⚠ Bidang dengan code {$subCode} tidak ditemukan di database.");
        //         Log::warning('Bidang not found in database', ['code' => $subCode, 'program' => $code]);
        //     }
        // }

        $versi = $item['version'] ?? 1;
        $tanggal = null;
        if (!empty($item['effective_date'])) {
            try {
                $tanggal = Carbon::parse($item['effective_date']);
            } catch (\Exception $e) {
                if (!empty($item['created_at'])) {
                    try {
                        $tanggal = Carbon::parse($item['created_at']);
                    } catch (\Exception $e2) {}
                }
            }
        } elseif (!empty($item['created_at'])) {
            try {
                $tanggal = Carbon::parse($item['created_at']);
            } catch (\Exception $e) {}
        }

        $durationHours = $item['total_duration'] ?? $item['total_topic_duration'] ?? 0;

        $existing = MasterProgram::where('code', $code)->first();
        $isNew = !$existing;

        $programData = [
            'name'                  => $item['title'] ?? 'Unknown Title',
            'program_pelatihan'     => $item['title'] ?? 'Unknown',
            'description'           => $item['description'] ?? null,
            'duration_hours'        => (int) $durationHours,
            'kejuruan_id'           => $kejuruanId,
            'bidang_pelatihan_id'   => $bidangId,
            'versi'                 => $versi,
            'tanggal'               => $tanggal,
            'is_active'             => ($item['status'] ?? 'pending') === 'approved',
            'updated_by'            => 1, // System user
        ];

        // Set created_by hanya untuk data baru
        if ($isNew) {
            $programData['created_by'] = 1;
        }

        // Preserve existing file if any
        if ($existing && $existing->file_program && str_starts_with($existing->file_program, 'program-files/')) {
            $programData['file_program'] = $existing->file_program;
        }

        $master = MasterProgram::updateOrCreate(
            ['code' => $code],
            $programData
        );

        if ($isNew) {
            $this->syncedPrograms++;
        } else if ($master->wasChanged()) {
            $this->updatedPrograms++;
        }

        // Download file
        if (!$skipFiles && !empty($item['material_value'] ?? $item['download_material_uri'] ?? $item['document'] ?? null)) {
            $fileUri = $item['download_material_uri'] ?? $item['material_value'] ?? $item['document'] ?? null;
            if ($fileUri) {
                $result = $this->downloadFile($programId, $fileUri, $master, $debug);
                if ($result === 'success') $this->filesDownloaded++;
                elseif ($result === 'skipped') $this->filesSkipped++;
                else $this->filesFailed++;
            }
        }

        // Sync units
        if (!empty($item['program_topics']) && is_array($item['program_topics'])) {
            foreach ($item['program_topics'] as $topic) {
                $topicCode = $topic['code'] ?? null;
                if (empty($topicCode) || $topicCode === '-' || strlen($topicCode) < 3) continue;

                try {
                    $unit = IndependentCompetencyUnit::updateOrCreate(
                        ['code' => $topicCode],
                        [
                            'name' => $topic['title'] ?? 'Unknown Unit',
                            'description' => $topic['description'] ?? null,
                            'skkni_id' => $topic['skkni_id'] ?? null,
                        ]
                    );

                    ProgramPelatihanUnits::updateOrCreate(
                    [
                        'master_programs_id' => $master->id,
                        'independent_competency_units_id' => $unit->id,
                    ],
                    [
                        'program_pelatihan_id' => $master->id,  // ← Tambahkan ini! (hubungkan ke master sebagai program pelatihan juga)
                        'type_unit' => $topic['type'] ?? 'skkni',  // ← Ganti default ke 'skkni' (lebih masuk akal untuk Kemnaker)
                        'jp' => $topic['duration'] ?? 0,
                        'sub_unit_kompetensi' => 'N',
                        'name' => $topic['title'] ?? $unit->name ?? 'Unit Tanpa Nama',  // ← Opsional: tambah kalau sudah ada kolom name
                    ]
                );

                    $this->syncedUnits++;
                } catch (Exception $e) {
                    // silent
                }
            }
        }

        if ($debug) {
            $this->info("   ✓ {$code} | V{$versi} | {$durationHours}h | " . ($tanggal ? $tanggal->format('Y-m-d') : 'no-date'));
        }
    }

    private function updateNullData($skipFiles, $debug)
    {
        $this->info('🔍 Mencari program dengan data null...');
        
        // Cari program yang punya data null
        $nullPrograms = MasterProgram::where(function($query) {
            $query->whereNull('kejuruan_id')
                  ->orWhereNull('bidang_pelatihan_id')
                  ->orWhereNull('file_program')
                  ->orWhereNull('created_by')
                  ->orWhereNull('updated_by');
        })->get();
        
        $this->info("   Ditemukan {$nullPrograms->count()} program dengan data null");
        
        if ($nullPrograms->isEmpty()) {
            $this->info('✅ Semua data sudah lengkap!');
            return;
        }
        
        $bar = $this->output->createProgressBar($nullPrograms->count());
        $bar->start();
        
        foreach ($nullPrograms as $program) {
            try {
                // Ambil detail dari API berdasarkan code program
                $response = Http::withOptions(['verify' => false, 'timeout' => 30])
                    ->get('https://api.kemnaker.go.id/proglat/v1/public/programs', [
                        'limit' => 1,
                        'keyword' => $program->code,
                    ]);
                
                if (!$response->successful() || empty($response->json()['data'])) {
                    // Coba cari by name
                    $response = Http::withOptions(['verify' => false, 'timeout' => 30])
                        ->get('https://api.kemnaker.go.id/proglat/v1/public/programs', [
                            'limit' => 1,
                            'keyword' => $program->name,
                        ]);
                }
                
                if ($response->successful() && !empty($response->json()['data'][0])) {
                    $item = $response->json()['data'][0];
                    
                    $updateData = [];
                    
                    // Update kejuruan jika null - AMBIL DARI DATABASE BERDASARKAN CODE
                    if (is_null($program->kejuruan_id)) {
                        $vocCode = $item['vocational']['code'] ?? null;
                        
                        if (!empty($vocCode)) {
                            $kejuruan = Kejuruan::where('code', $vocCode)->first();
                            
                            if ($kejuruan) {
                                $updateData['kejuruan_id'] = $kejuruan->id;
                                if ($debug) {
                                    $this->line("   ✓ Kejuruan diupdate: {$kejuruan->kejuruan} untuk program {$program->code}");
                                }
                            } else {
                                if ($debug) {
                                    $this->warn("   ⚠ Kejuruan code {$vocCode} tidak ada di database untuk program {$program->code}");
                                }
                            }
                        }
                    }
                    
                    // Update bidang jika null - AMBIL DARI DATABASE BERDASARKAN CODE
                    if (is_null($program->bidang_pelatihan_id)) {
                        $subCode = $item['sub_vocational']['code'] ?? null;
                        
                        if (!empty($subCode)) {
                            $bidang = BidangPelatihan::where('code', $subCode)->first();
                            
                            if ($bidang) {
                                $updateData['bidang_pelatihan_id'] = $bidang->id;
                                if ($debug) {
                                    $this->line("   ✓ Bidang diupdate: {$bidang->bidang_pelatihan} untuk program {$program->code}");
                                }
                            } else {
                                if ($debug) {
                                    $this->warn("   ⚠ Bidang code {$subCode} tidak ada di database untuk program {$program->code}");
                                }
                            }
                        }
                    }
                    
                    // Update created_by/updated_by jika null
                    if (is_null($program->created_by)) {
                        $updateData['created_by'] = 1;
                    }
                    if (is_null($program->updated_by)) {
                        $updateData['updated_by'] = 1;
                    }
                    
                    // Download file jika null dan ada di API
                    if (is_null($program->file_program) && !$skipFiles) {
                        $fileUri = $item['download_material_uri'] ?? $item['material_value'] ?? $item['document'] ?? null;
                        if ($fileUri) {
                            $result = $this->downloadFile($item['id'], $fileUri, $program, $debug);
                            if ($result === 'success') {
                                $this->filesDownloaded++;
                            }
                        }
                    }
                    
                    // Update jika ada perubahan
                    if (!empty($updateData)) {
                        $program->update($updateData);
                        $this->updatedPrograms++;
                    }
                }
                
            } catch (Exception $e) {
                if ($debug) {
                    $this->error("   Error update {$program->code}: " . $e->getMessage());
                }
            }
            
            $bar->advance();
        }
        
        $bar->finish();
        $this->newLine();
        
        $this->info("✅ Update data null selesai!");
        $this->info("   {$this->updatedPrograms} program diupdate");
        $this->info("   {$this->filesDownloaded} file didownload");
    }

    private function downloadFile($programId, $fileUri, $master, $debug)
    {
        try {
            $extension = pathinfo($fileUri, PATHINFO_EXTENSION) ?: 'pdf';
            $safeCode = preg_replace('/[^A-Za-z0-9\-]/', '-', $master->code);
            $filename = $safeCode . '.' . $extension;
            $localPath = "program-files/{$filename}";

            if ($master->file_program && Storage::disk('public')->exists($master->file_program)) {
                if ($debug) $this->line("   ⏭️ Skip download (sudah ada)");
                return 'skipped';
            }

            $fullUrl = str_starts_with($fileUri, 'http') ? $fileUri : "https://proglat-assets.kemnaker.go.id/{$fileUri}";

            $response = Http::withOptions([
                'verify' => false,
                'timeout' => $this->downloadTimeout,
            ])->get($fullUrl);

            if (!$response->successful() || strlen($response->body()) < 500) {
                if ($debug) $this->warn("   Download gagal dari {$fullUrl} (size kecil atau HTTP {$response->status()})");
                return 'failed';
            }

            Storage::disk('public')->put($localPath, $response->body());

            if (Storage::disk('public')->exists($localPath)) {
                $master->update(['file_program' => $localPath]);
                if ($debug) $this->info("   📥 File disimpan: {$localPath}");
                return 'success';
            }

            return 'failed';

        } catch (Exception $e) {
            if ($debug) $this->error("   Download error: " . $e->getMessage());
            return 'failed';
        }
    }
}