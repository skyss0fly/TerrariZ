<?php
namespace TerrariZ\Utils;
use date;

class Logger
{
    const TYPES = ['info', 'debug', 'warning', 'error', 'emergency'];

    public static function log(string $type = 'info', string $message = ''): void
    {
        if (!in_array($type, self::TYPES)) {
            $type = 'info';
        }

        if (trim($message) === '') {
            $type = 'error';
            $message = 'Please input a non-empty string';
        }

        $timestamp = date('Y-m-d H:i:s');
        echo "[{$timestamp}] [{$type}] {$message}\n";
    }
}
?>
