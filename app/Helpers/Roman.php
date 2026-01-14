<?php

namespace App\Helpers;

class Roman
{
    public static function convert($number)
    {
        if ($number <= 0) return '-';

        $roman = '';
        $map = [
            'M' => 1000, 'CM' => 900, 'D' => 500, 'CD' => 400,
            'C' => 100, 'XC' => 90, 'L' => 50, 'XL' => 40,
            'X' => 10, 'IX' => 9, 'V' => 5, 'IV' => 4, 'I' => 1
        ];

        foreach ($map as $romanNum => $value) {
            $matches = intval($number / $value);
            $roman .= str_repeat($romanNum, $matches);
            $number = $number % $value;
        }

        return $roman;
    }
}