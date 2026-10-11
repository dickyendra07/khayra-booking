<?php

namespace App\Support;

class WhatsappLink
{
    /**
     * Normalise an Indonesian phone number to the international digits-only
     * form wa.me expects (e.g. "0812-3456-7890" => "6281234567890").
     * Returns null when the value does not look like a usable number.
     */
    public static function normalize(?string $raw): ?string
    {
        $digits = preg_replace('/\D+/', '', (string) $raw);

        if ($digits === '') {
            return null;
        }

        if (str_starts_with($digits, '0')) {
            $digits = '62'.substr($digits, 1);
        } elseif (str_starts_with($digits, '8')) {
            $digits = '62'.$digits;
        }

        return strlen($digits) >= 10 && strlen($digits) <= 15 ? $digits : null;
    }

    public static function url(string $normalizedNumber, string $message): string
    {
        return 'https://wa.me/'.$normalizedNumber.'?text='.rawurlencode($message);
    }
}
