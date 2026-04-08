<?php

namespace Database\Seeders;

use App\Models\DocumentSetting;
use Illuminate\Database\Seeder;

class DocumentSettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            [
                'key' => 'sk-peserta',
                'name' => 'SPT Peserta',
                'dasar_hukum_1' => 'Peraturan Menteri Ketenagakerjaan Republik Indonesia Nomor 8 Tahun 2014 tentang Pedoman Penyelenggaraan Pelatihan Berbasis Kompetensi',
                'dasar_hukum_2' => 'Keputusan Menteri Ketenagakerjaan Republik Indonesia Nomor 248 Tahun 2023 tentang Penetapan Standar Kompetensi Kerja Nasional Indonesia',
                'dasar_hukum_3' => 'Peraturan Menteri Ketenagakerjaan Republik Indonesia Nomor 1 Tahun 2022 tentang Tata Cara Pemberian dan Pemanfaatan Insentif Pajak Penghasilan',
                'dasar_hukum_4' => 'Surat Keputusan Kepala Balai Pelatihan Vokasi dan Produktivitas Nomor  2.8/7942/LP.00.04/VIII/2024  tentang Penyelenggaraan Project Based Kompetensi (PBK)',
                'dasar_hukum_5' => 'Daftar Isian Pelaksanaan Anggaran (SP-DIPA) Balai Pelatihan Vokasi dan Produktivitas Banda Aceh Tahun 2024 Nomor : 026.13.2.065106/2023 tanggal 28 November 2023. ',
                'kop_surat' => "KEMENTERIAN KETENAGAKERJAAN REPUBLIK INDONESIA\nDIREKTORAT JENDERAL PEMBINAAN PELATIHAN VOKASI DAN PRODUKTIVITAS\nBALAI PELATIHAN VOKASI DAN PRODUKTIVITAS BANDA ACEH",
                'format_nomor' => 'Nomor 2.8/7942/LP.00.04/VIII/2024 ',
                'tempat_surat' => 'Banda Aceh',
                'ttd_pengirim' => '${ttd_pengirim}',
                'nama_pengirim' => '${nama_pengirim}',
                'nip_pengirim' => '${nip_pengirim}',
            ],
            [
                'key' => 'sk-penyelenggara',
                'name' => 'SK Penyelenggara',
                'dasar_hukum_1' => 'Peraturan Menteri Ketenagakerjaan Republik Indonesia Nomor 8 Tahun 2014',
                'dasar_hukum_2' => 'Keputusan Menteri Ketenagakerjaan Republik Indonesia Nomor 248 Tahun 2023',
                'dasar_hukum_3' => null,
                'dasar_hukum_4' => null,
                'dasar_hukum_5' => null,
                'kop_surat' => "KEMENTERIAN KETENAGAKERJAAN REPUBLIK INDONESIA\nDIREKTORAT JENDERAL PEMBINAAN PELATIHAN VOKASI DAN PRODUKTIVITAS\nBALAI PELATIHAN VOKASI DAN PRODUKTIVITAS BANDA ACEH",
                'format_nomor' => '{nomor}/SK-PSLG/BPVP/{tahun}',
                'tempat_surat' => 'Banda Aceh',
                'ttd_pengirim' => '${ttd_pengirim}',
                'nama_pengirim' => '${nama_pengirim}',
                'nip_pengirim' => '${nip_pengirim}',
            ],
            [
                'key' => 'st-instruktur',
                'name' => 'ST Instruktur',
                'dasar_hukum_1' => 'Peraturan Menteri Ketenagakerjaan Republik Indonesia Nomor 8 Tahun 2014',
                'dasar_hukum_2' => 'Keputusan Kepala BPVP Banda Aceh',
                'dasar_hukum_3' => null,
                'dasar_hukum_4' => null,
                'dasar_hukum_5' => null,
                'kop_surat' => "KEMENTERIAN KETENAGAKERJAAN REPUBLIK INDONESIA\nDIREKTORAT JENDERAL PEMBINAAN PELATIHAN VOKASI DAN PRODUKTIVITAS\nBALAI PELATIHAN VOKASI DAN PRODUKTIVITAS BANDA ACEH",
                'format_nomor' => '{nomor}/ST-INS/BPVP/{tahun}',
                'tempat_surat' => 'Banda Aceh',
                'ttd_pengirim' => '${ttd_pengirim}',
                'nama_pengirim' => '${nama_pengirim}',
                'nip_pengirim' => '${nip_pengirim}',
            ],
            ['key' => 'jadwal', 'name' => 'Jadwal Pelatihan'],
            ['key' => 'daftar-hadir', 'name' => 'Daftar Hadir'],
            ['key' => 'biodata-peserta', 'name' => 'Biodata Peserta'],
            [
                'key' => 'jadwal',
                'name' => 'Jadwal Pelatihan',
                'dasar_hukum_1' => null,
                'dasar_hukum_2' => null,
                'dasar_hukum_3' => null,
                'dasar_hukum_4' => null,
                'dasar_hukum_5' => null,
                'kop_surat' => "KEMENTERIAN KETENAGAKERJAAN REPUBLIK INDONESIA\nDIREKTORAT JENDERAL PEMBINAAN PELATIHAN VOKASI DAN PRODUKTIVITAS\nBALAI PELATIHAN VOKASI DAN PRODUKTIVITAS BANDA ACEH",
                'format_nomor' => null,
                'tempat_surat' => 'Banda Aceh',
            ],
            [
                'key' => 'daftar-hadir',
                'name' => 'Daftar Hadir',
                'dasar_hukum_1' => null,
                'dasar_hukum_2' => null,
                'dasar_hukum_3' => null,
                'dasar_hukum_4' => null,
                'dasar_hukum_5' => null,
                'kop_surat' => "KEMENTERIAN KETENAGAKERJAAN REPUBLIK INDONESIA\nDIREKTORAT JENDERAL PEMBINAAN PELATIHAN VOKASI DAN PRODUKTIVITAS\nBALAI PELATIHAN VOKASI DAN PRODUKTIVITAS BANDA ACEH",
                'format_nomor' => null,
                'tempat_surat' => 'Banda Aceh',
            ],
            [
                'key' => 'biodata-peserta',
                'name' => 'Biodata Peserta',
                'dasar_hukum_1' => null,
                'dasar_hukum_2' => null,
                'dasar_hukum_3' => null,
                'dasar_hukum_4' => null,
                'dasar_hukum_5' => null,
                'kop_surat' => "KEMENTERIAN KETENAGAKERJAAN REPUBLIK INDONESIA\nDIREKTORAT JENDERAL PEMBINAAN PELATIHAN VOKASI DAN PRODUKTIVITAS\nBALAI PELATIHAN VOKASI DAN PRODUKTIVITAS BANDA ACEH",
                'format_nomor' => null,
                'tempat_surat' => 'Banda Aceh',
            ],
        ];

        foreach ($settings as $setting) {
            DocumentSetting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }

        $this->command->info('✅ Document settings seeded successfully (6 templates)!');
    }
}