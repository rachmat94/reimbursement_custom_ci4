<?php
namespace App\Libraries;

class RowKeyGeneratorv2
{
    /**
     * Generate readable, unique, and fast row_key.
     * If failed, fallback to formatted UUID v4 (pseudo UUID v4).
     */
    public static function generate(string $prefix = '', string $suffix = ''): string
    {
        try {
            return self::generateOptimized($prefix, $suffix);
        } catch (\Throwable $e) {
            log_message('error', 'Fallback to formatted UUID v4: ' . $e->getMessage());
            return self::generateFormattedUUIDv4();
        }
    }

    /**
     * Main generator with time + randomness.
     */
    private static function generateOptimized(string $prefix = '', string $suffix = ''): string
    {
        // Base36 time (microtime) ensures uniqueness per call
        $timestamp = base_convert((int)(microtime(true) * 10000), 10, 36);

        // Random entropy
        $rand1 = bin2hex(random_bytes(3)); // 6 chars
        $rand2 = bin2hex(random_bytes(4)); // 8 chars
        $rand3 = bin2hex(random_bytes(3)); // 6 chars
        $rand4 = bin2hex(random_bytes(3)); // 6 chars

        // Combine all parts
        // $keys = array_filter([
        //     $prefix,
        //     $timestamp,
        //     strtoupper($rand1),
        //     strtoupper($rand2),
        //     strtoupper($rand3),
        //     $suffix
        // ]);
        // Combine all parts
        $keys = array_filter([
            $prefix,
            $timestamp,
            ($rand1),
            ($rand2),
            ($rand3),
            ($rand4),
            $suffix
        ]);

        return implode('-', $keys);
    }

    /**
     * Formatted (pseudo) UUID v4 generator (fallback).
     */
    private static function generateFormattedUUIDv4(): string
    {
        $data = random_bytes(16);
        $data[6] = chr((ord($data[6]) & 0x0f) | 0x40); // Version 4
        $data[8] = chr((ord($data[8]) & 0x3f) | 0x80); // Variant

        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }
}
