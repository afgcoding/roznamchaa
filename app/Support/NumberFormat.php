<?php

namespace App\Support;

class NumberFormat
{
    /**
     * Format a number without trailing zeros (5.000 → 5, 5.50 → 5.5).
     */
    public static function trim(mixed $value, int $maxDecimals = 3): string
    {
        if ($value === null || $value === '') {
            return '';
        }

        $formatted = rtrim(rtrim(number_format((float) $value, $maxDecimals, '.', ''), '0'), '.');

        return $formatted === '' ? '0' : $formatted;
    }
}
