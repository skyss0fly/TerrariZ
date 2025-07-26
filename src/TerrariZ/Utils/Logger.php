<?php
namespace TerrariZ\Utils;

class Logger
{
    // ANSI color codes
    private const RED = "\033[31m";      // Error, Emergency
    private const YELLOW = "\033[33m";   // Warning
    private const BLUE = "\033[34m";     // Debug
    private const RESET = "\033[0m";     // Reset
    private const DEFAULT = "";          // Info (no color)

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
        $color = self::getColor($type);

        echo "{$color}[{$timestamp}] [{$type}] {$message}" . self::RESET . "\n";
    }

    private static function getColor(string $type): string
    {
        return match ($type) {
            'error', 'emergency' => self::RED,
            'warning' => self::YELLOW,
            'debug' => self::BLUE,
            default => self::DEFAULT,
        };
    }
}
?>
