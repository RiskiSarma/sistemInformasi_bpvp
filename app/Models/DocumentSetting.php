<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'name',
        'dasar_hukum_1',
        'dasar_hukum_2',
        'dasar_hukum_3',
        'dasar_hukum_4',
        'dasar_hukum_5',
        'logo_path',
        'kop_surat',
        'format_nomor',
        'tempat_surat',
        'ttd_pengirim',
        'nama_pengirim',
        'nip_pengirim',
    ];

    /**
     * Get default settings for a template
     */
    public static function getDefaults($key)
    {
        $defaults = [
            'sk-peserta' => [
                'dasar_hukum_1' => 'Peraturan Menteri Ketenagakerjaan Republik Indonesia Nomor 8 Tahun 2014 tentang Pedoman Penyelenggaraan Pelatihan Berbasis Kompetensi',
                'dasar_hukum_2' => 'Keputusan Menteri Ketenagakerjaan Republik Indonesia Nomor 248 Tahun 2023 tentang Penetapan Standar Kompetensi Kerja Nasional Indonesia',
                'dasar_hukum_3' => 'Peraturan Menteri Ketenagakerjaan Republik Indonesia Nomor 1 Tahun 2022 tentang Tata Cara Pemberian dan Pemanfaatan Insentif Pajak Penghasilan',
                'dasar_hukum_4' => 'Surat Keputusan Kepala Balai Pelatihan Vokasi dan Produktivitas Nomor ___/___/{tahun} tentang Penyelenggaraan Pelatihan',
                'dasar_hukum_5' => 'Daftar Isian Pelaksanaan Anggaran (SP-DIPA) Balai Pelatihan Vokasi dan Produktivitas Banda Aceh Tahun {tahun} Nomor: 026.13.2.065106/{tahun_sebelumnya}',
                'kop_surat' => 'KEMENTERIAN KETENAGAKERJAAN REPUBLIK INDONESIA
BALAI PELATIHAN DAN PRODUKTIVITAS BANDA ACEH',
                'format_nomor' => 'Nomor: {nomor}/BPVP-BA/{tahun}',
                'tempat_surat' => 'Banda Aceh',
                'ttd_pengirim' => '${ttd_pengirim}',
                'nama_pengirim' => '${nama_pengirim}',
                'nip_pengirim' => '${nip_pengirim}',
            ],
            'sk-penyelenggara' => [
                'dasar_hukum_1' => 'Peraturan Menteri Ketenagakerjaan Republik Indonesia Nomor 8 Tahun 2014',
                'dasar_hukum_2' => 'Keputusan Menteri Ketenagakerjaan Republik Indonesia Nomor 248 Tahun 2023',
                'dasar_hukum_3' => '',
                'kop_surat' => 'KEMENTERIAN KETENAGAKERJAAN REPUBLIK INDONESIA
BALAI PELATIHAN DAN PRODUKTIVITAS BANDA ACEH',
                'format_nomor' => 'Nomor: {nomor}/SK-PSLG/BPVP/{tahun}',
                'tempat_surat' => 'Banda Aceh',
                'ttd_pengirim' => '${ttd_pengirim}',
                'nama_pengirim' => '${nama_pengirim}',
                'nip_pengirim' => '${nip_pengirim}',
            ],
            'st-instruktur' => [
                'dasar_hukum_1' => 'Peraturan Menteri Ketenagakerjaan Republik Indonesia Nomor 8 Tahun 2014',
                'dasar_hukum_2' => 'Keputusan Kepala BPVP Banda Aceh',
                'dasar_hukum_3' => '',
                'kop_surat' => 'KEMENTERIAN KETENAGAKERJAAN REPUBLIK INDONESIA
BALAI PELATIHAN DAN PRODUKTIVITAS BANDA ACEH',
                'format_nomor' => 'Nomor: {nomor}/ST-INS/BPVP/{tahun}',
                'tempat_surat' => 'Banda Aceh',
                'ttd_pengirim' => '${ttd_pengirim}',
                'nama_pengirim' => '${nama_pengirim}',
                'nip_pengirim' => '${nip_pengirim}',
            ],
            'jadwal' => [
                'dasar_hukum_1' => '',
                'dasar_hukum_2' => '',
                'dasar_hukum_3' => '',
                'kop_surat' => 'KEMENTERIAN KETENAGAKERJAAN REPUBLIK INDONESIA
BALAI PELATIHAN DAN PRODUKTIVITAS BANDA ACEH',
                'format_nomor' => '',
                'tempat_surat' => 'Banda Aceh',
            ],
            'daftar-hadir' => [
                'dasar_hukum_1' => '',
                'dasar_hukum_2' => '',
                'dasar_hukum_3' => '',
                'kop_surat' => 'KEMENTERIAN KETENAGAKERJAAN REPUBLIK INDONESIA
BALAI PELATIHAN DAN PRODUKTIVITAS BANDA ACEH',
                'format_nomor' => '',
                'tempat_surat' => 'Banda Aceh',
            ],
            'biodata-peserta' => [
                'dasar_hukum_1' => '',
                'dasar_hukum_2' => '',
                'dasar_hukum_3' => '',
                'kop_surat' => 'KEMENTERIAN KETENAGAKERJAAN REPUBLIK INDONESIA
BALAI PELATIHAN DAN PRODUKTIVITAS BANDA ACEH',
                'format_nomor' => '',
                'tempat_surat' => 'Banda Aceh',
            ],
        ];

        return $defaults[$key] ?? [];
    }
}