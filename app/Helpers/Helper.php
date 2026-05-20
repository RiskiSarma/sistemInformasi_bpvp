<?php

use App\Helpers\Roman;

if (!function_exists('formatAngkatan')) {
    function formatAngkatan($angkatan): string
    {
        if (empty($angkatan)) return '-';

        if (is_numeric($angkatan)) {
            return Roman::convert((int) $angkatan);
        }

        return $angkatan;
    }
}