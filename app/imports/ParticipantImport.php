<?php

namespace App\Imports;

use App\Models\User;
use App\Models\Participant;
use App\Models\Pendidikan;
use App\Models\Program;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ParticipantImport implements ToModel, WithHeadingRow, SkipsOnError, WithMultipleSheets
{
    use SkipsErrors;

    protected $programId;
    protected $createdBy;
    protected $programKeyword;
    public $importedCount = 0;
    public $updatedCount  = 0;
    public $skippedCount  = 0;
    public $filteredCount = 0;
    public $importErrors  = [];

    protected $pendidikanCache = [];
    protected $isFirstRow = true;

    public function __construct($programId, $createdBy)
    {
        $this->programId = $programId;
        $this->createdBy = $createdBy;

        set_time_limit(300);

        $program = Program::with('masterProgram')->find($programId);
        if ($program && $program->masterProgram) {
            $this->programKeyword = strtoupper($program->masterProgram->name);
        }

        $this->pendidikanCache = Pendidikan::all()->keyBy(fn($p) => strtolower($p->pendidikan));
    }

    public function sheets(): array
    {
        return [0 => $this];
    }

    public function model(array $row)
    {
        // Debug: log header dan baris pertama sekali saja
        if ($this->isFirstRow) {
            Log::info('=== EXCEL HEADERS ===', array_keys($row));
            Log::info('=== EXCEL ROW PERTAMA ===', $row);
            $this->isFirstRow = false;
        }

        // Ambil nama & email
        $nama = trim(
            $row['nama_lengkap_sesuai_ijazah'] ??
            $row['nama_lengkap'] ??
            $row['nama'] ??
            ''
        );
        $email = strtolower(trim(
            $row['email_address'] ??
            $row['email'] ??
            $row['alamat_email'] ??
            ''
        ));

        if (empty($nama) || empty($email)) {
            $this->skippedCount++;
            return null;
        }

        // Filter berdasarkan program
        if ($this->programKeyword) {
            $excelProgram = strtoupper(trim(
                $row['program_pelatihan'] ??
                $row['program'] ??
                ''
            ));
            if (!empty($excelProgram) && !$this->isProgramMatch($excelProgram, $this->programKeyword)) {
                $this->filteredCount++;
                return null;
            }
        }

        // Ambil NIK mentah dulu sebelum dibersihkan
        $nikRaw = trim((string)(
            $row['nik_no_ktp']  ??
            $row['nikno_ktp']   ??
            $row['nik_no__ktp'] ??
            $row['nik']         ??
            $row['no_ktp']      ??
            $row['ktp']         ??
            $row['nik_ktp']     ??
            ''
        ));

        // Log NIK mentah untuk setiap peserta — hapus setelah masalah NIK selesai
        Log::info("NIK mentah untuk [{$nama}]: '{$nikRaw}'");

        // Bersihkan NIK — hanya angka, max 16 digit
        $nik = preg_replace('/[^0-9]/', '', $nikRaw);
        if (strlen($nik) > 16) {
            $nik = substr($nik, 0, 16);
        }

        // Ambil field lainnya
        $telepon = $this->normalizePhone(trim((string)(
            $row['nomor_hp_aktif'] ??
            $row['no_hp']          ??
            $row['telepon']        ??
            $row['hp']             ??
            $row['phone']          ??
            ''
        )));

        $jenisKelamin = $this->normalizeGender(trim(
            $row['jenis_kelamin'] ??
            $row['kelamin']       ??
            ''
        ));

        $tempatLahir = trim(
            $row['tempat_lahir_sesuai_ijazah'] ??
            $row['tempat_lahir']               ??
            ''
        );

        $tglLahir =
            $row['tanggal_lahir_sesuai_ijazah'] ??
            $row['tanggal_lahir']               ??
            $row['tgl_lahir']                   ??
            null;

        $pendidikanRaw = trim(
            $row['pendidikan_terakhir_sesuai_ijazah'] ??
            $row['pendidikan_terakhir']               ??
            $row['pendidikan']                        ??
            ''
        );

        $alamat = trim(
            $row['alamat_sesuai_ktp'] ??
            $row['alamat']            ??
            ''
        );

        // Parse tanggal lahir
        $birthDate = $this->parseBirthDate($tglLahir);

        // Cari pendidikan dari cache
        $pendidikan = $this->findPendidikan($pendidikanRaw);

        // Log jika NIK kosong
        if (empty($nik) && !empty($nama)) {
            Log::warning("NIK kosong setelah dibersihkan untuk: {$nama} ({$email})");
        }

        // Data participant
        $participantData = [
            'program_id'    => $this->programId,
            'nik'           => !empty($nik) ? $nik : null,
            'phone'         => $telepon,
            'gender'        => $jenisKelamin,
            'birth_place'   => !empty($tempatLahir) ? $tempatLahir : null,
            'birth_date'    => $birthDate,
            'pendidikan_id' => $pendidikan?->id,
            'address'       => !empty($alamat) ? $alamat : null,
            'status'        => 'active',
            'updated_by'    => $this->createdBy,
        ];

        // Cek user sudah ada atau belum
        $user = User::where('email', $email)->first();

        if ($user) {
            $user->update(['name' => $nama]);

            $participant = $user->participant;

            if ($participant) {
                $participant->update($participantData);
                $this->updatedCount++;
            } else {
                Participant::create(array_merge($participantData, [
                    'user_id'    => $user->id,
                    'created_by' => $this->createdBy,
                ]));
                $this->importedCount++;
            }
        } else {
            // Password default = NIK → nomor HP → 'Password@123'
            $defaultPassword = !empty($nik)
                ? $nik
                : (!empty($telepon) ? $telepon : 'Password@123');

            Log::info("Buat user baru: {$nama} | email: {$email} | password: {$defaultPassword}");

            $user = User::create([
                'name'       => $nama,
                'email'      => $email,
                'password'   => Hash::make($defaultPassword), // tanpa ['rounds' => 4]
                'role'       => 'participant',
                'created_by' => $this->createdBy,
                'updated_by' => $this->createdBy,
            ]);

            Participant::create(array_merge($participantData, [
                'user_id'    => $user->id,
                'created_by' => $this->createdBy,
            ]));

            $this->importedCount++;
        }

        return null;
    }

    private function normalizeGender(string $value): ?string
    {
        $value = strtoupper(trim($value));

        if (in_array($value, ['LAKI-LAKI', 'LAKI LAKI', 'L', 'PRIA', 'MALE'])) {
            return 'LAKI-LAKI';
        }

        if (in_array($value, ['PEREMPUAN', 'P', 'WANITA', 'FEMALE'])) {
            return 'PEREMPUAN';
        }

        return !empty($value) ? $value : null;
    }

    private function findPendidikan(string $raw): ?object
    {
        if (empty($raw)) return null;

        $key = strtolower(trim($raw));

        // Exact match (case insensitive)
        if (isset($this->pendidikanCache[$key])) {
            return $this->pendidikanCache[$key];
        }

        // Tidak ketemu
        Log::warning("Pendidikan tidak dikenali: '{$raw}'");
        $this->importErrors[] = "Pendidikan '{$raw}' tidak dikenali, diisi kosong.";
        return null;
    }

    private function parseBirthDate(mixed $value): ?string
    {
        if (empty($value)) return null;

        try {
            if ($value instanceof \DateTime) {
                $year = (int) $value->format('Y');
                if ($year < 1900 || $year > (int) date('Y')) return null;
                return $value->format('Y-m-d');
            }

            if (is_numeric($value)) {
                $date = \Carbon\Carbon::createFromFormat('Y', '1900')
                    ->startOfYear()
                    ->addDays((int)$value - 2);
                if ($date->year < 1900 || $date->year > (int) date('Y')) return null;
                return $date->format('Y-m-d');
            }

            // String — coba berbagai format
            $formats = ['d/m/Y', 'd-m-Y', 'Y-m-d', 'd/m/y', 'Y/m/d'];
            foreach ($formats as $format) {
                try {
                    $parsed = \Carbon\Carbon::createFromFormat($format, trim((string)$value));
                    if ($parsed && $parsed->year >= 1900 && $parsed->year <= (int) date('Y')) {
                        return $parsed->format('Y-m-d');
                    }
                } catch (\Exception $e) {
                    continue;
                }
            }

            $parsed = \Carbon\Carbon::parse((string)$value);
            if ($parsed->year < 1900 || $parsed->year > (int) date('Y')) return null;
            return $parsed->format('Y-m-d');

        } catch (\Exception $e) {
            Log::warning("Gagal parse tanggal lahir: " . json_encode($value));
            return null;
        }
    }

    private function isProgramMatch(string $excelProgram, string $keyword): bool
    {
        if (str_contains($excelProgram, $keyword)) return true;
        $keywordWords = array_filter(explode(' ', $keyword), fn($w) => strlen($w) > 3);
        $matchCount = 0;
        foreach ($keywordWords as $word) {
            if (str_contains($excelProgram, $word)) $matchCount++;
        }
        $threshold = max(1, (int) ceil(count($keywordWords) * 0.7));
        return $matchCount >= $threshold;
    }

    private function normalizePhone(string $value): ?string
    {
        if (empty($value)) return null;

        foreach (['/', ',', ' / ', ' , ', ';'] as $sep) {
            if (str_contains($value, $sep)) {
                $value = explode($sep, $value)[0];
                break;
            }
        }

        $value = trim($value);
        $value = preg_replace('/[^0-9+]/', '', $value);

        if (str_starts_with($value, '+62')) {
            $value = '0' . substr($value, 3);
        }

        return !empty($value) ? substr($value, 0, 20) : null;
    }
}