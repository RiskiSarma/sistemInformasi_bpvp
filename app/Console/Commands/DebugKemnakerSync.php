<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\MasterProgram;

class DebugKemnakerSync extends Command
{
    protected $signature = 'kemnaker:debug-sync';
    protected $description = 'Debug: cek exact code matching';

    public function handle()
    {
        // Ambil 5 sample code dari DB yang null (bukan MP/MJ)
        $samples = MasterProgram::whereNull('kejuruan_id')
            ->where('code', 'not like', 'MP%')
            ->where('code', 'not like', 'MJ%')
            ->where('code', 'not like', 'AUTO%')
            ->limit(5)
            ->get(['code', 'name']);

        $this->info('=== Sample code dari DB ===');
        foreach ($samples as $p) {
            $len  = strlen($p->code);
            $hex  = bin2hex($p->code); // untuk lihat karakter tersembunyi
            $this->line("  code: [{$p->code}] len:{$len}");
            $this->line("  hex:  [{$hex}]");
            $this->line('');
        }

        // Sekarang cari code yang sama persis di API
        $this->info('=== Cari di API (search by exact code) ===');
        $testCode = $samples->first()->code ?? null;
        if (!$testCode) {
            $this->error('Tidak ada sample');
            return 0;
        }

        $cleanCode = trim($testCode);
        $this->line("Mencari: [$cleanCode]");

        // Coba endpoint detail langsung by code (kalau ada)
        try {
            $r = Http::withOptions(['verify' => false, 'timeout' => 15])
                ->get('https://api.kemnaker.go.id/proglat/v1/public/programs', [
                    'limit' => 5,
                    'code'  => $cleanCode,
                ]);
            $data = $r->json()['data'] ?? [];
            $this->line("Search ?code={$cleanCode} → " . count($data) . " hasil");
            foreach ($data as $item) {
                $apiCode = $item['code'] ?? 'N/A';
                $apiLen  = strlen($apiCode);
                $this->line("  API code: [{$apiCode}] len:{$apiLen}");
                // Bandingkan karakter per karakter
                if (strtoupper(trim($apiCode)) === strtoupper($cleanCode)) {
                    $this->info("  ✅ EXACT MATCH!");
                } else {
                    $this->warn("  ❌ Tidak sama");
                    // Cari perbedaan
                    $dbChars  = str_split(strtoupper($cleanCode));
                    $apiChars = str_split(strtoupper(trim($apiCode)));
                    $maxLen   = max(count($dbChars), count($apiChars));
                    for ($i = 0; $i < $maxLen; $i++) {
                        $db  = isset($dbChars[$i])  ? $dbChars[$i]  : '(end)';
                        $api = isset($apiChars[$i]) ? $apiChars[$i] : '(end)';
                        if ($db !== $api) {
                            $this->line("  Beda di pos {$i}: DB=[{$db}] API=[{$api}]");
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
        }

        // Coba search by title/name
        $sampleName = $samples->first()->name ?? '';
        $this->info('');
        $this->line("Mencari by name: [{$sampleName}]");
        try {
            $r2   = Http::withOptions(['verify' => false, 'timeout' => 15])
                ->get('https://api.kemnaker.go.id/proglat/v1/public/programs', [
                    'limit'   => 5,
                    'keyword' => $sampleName,
                ]);
            $data2 = $r2->json()['data'] ?? [];
            $this->line("Search ?keyword={$sampleName} → " . count($data2) . " hasil");
            foreach ($data2 as $item) {
                $apiCode  = $item['code'] ?? 'N/A';
                $apiTitle = $item['title'] ?? 'N/A';
                $this->line("  [{$apiCode}] {$apiTitle}");
            }
        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
        }

        // Cek meta/pagination dari API
        $this->info('');
        $this->info('=== Cek pagination API ===');
        try {
            $r3   = Http::withOptions(['verify' => false, 'timeout' => 15])
                ->get('https://api.kemnaker.go.id/proglat/v1/public/programs?limit=1&page=1');
            $json = $r3->json();
            $meta = $json['meta'] ?? [];
            $this->line("meta.total:     " . ($meta['total']     ?? 'N/A'));
            $this->line("meta.last_page: " . ($meta['last_page'] ?? 'N/A'));
            $this->line("meta.per_page:  " . ($meta['per_page']  ?? 'N/A'));
        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
        }

        return 0;
    }
}