<?php

namespace App\Libraries;

class FileLimiter
{
    /**
     * Batasi jumlah file di folder (termasuk subfolder).
     * 
     * @param string $path Path folder yang akan dicek
     * @param int $maxFiles Jumlah maksimal file
     * @return void
     */
    public static function limitFiles(string $path, int $maxFiles): void
    {
        if (!is_dir($path)) {
            return;
        }

        $allFiles = self::getAllFiles($path);

        // Jika jumlah file <= batas, tidak perlu hapus
        if (count($allFiles) <= $maxFiles) {
            return;
        }

        // Urutkan berdasarkan waktu modifikasi (paling lama dulu)
        usort($allFiles, function ($a, $b) {
            return filemtime($a) <=> filemtime($b);
        });

        // Hitung jumlah yang harus dihapus
        $deleteCount = count($allFiles) - $maxFiles;

        // Hapus file paling lama
        for ($i = 0; $i < $deleteCount; $i++) {
            @unlink($allFiles[$i]);
        }

        // Hapus folder kosong setelah file dihapus
        self::removeEmptyFolders($path);
    }

    /**
     * Ambil semua file dari folder (termasuk subfolder).
     * 
     * @param string $path
     * @return array
     */
    private static function getAllFiles(string $path): array
    {
        $files = [];
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($path, \FilesystemIterator::SKIP_DOTS)
        );

        foreach ($iterator as $file) {
            if ($file->isFile()) {
                $files[] = $file->getPathname();
            }
        }

        return $files;
    }

    /**
     * Hapus folder kosong secara rekursif.
     * 
     * @param string $path
     * @return void
     */
    private static function removeEmptyFolders(string $path): void
    {
        $isEmpty = true;
        foreach (scandir($path) as $file) {
            if ($file === '.' || $file === '..') {
                continue;
            }
            $fullPath = $path . DIRECTORY_SEPARATOR . $file;
            if (is_dir($fullPath)) {
                self::removeEmptyFolders($fullPath);
            }
        }

        // Jika folder kosong dan bukan root awal
        if ($path !== rtrim($path, DIRECTORY_SEPARATOR) && count(glob($path . '/*')) === 0) {
            @rmdir($path);
        }
    }
}
