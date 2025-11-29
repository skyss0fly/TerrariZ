<?php
namespace TerrariZ\Utils;

class Logger
{
    // ANSI color codes
    private const RED = "\033[31m";      
    private const YELLOW = "\033[33m";   
    private const BLUE = "\033[34m";     
    private const RESET = "\033[0m";     
    private const DEFAULT = "";          

    const TYPES = ['info', 'debug', 'warning', 'error', 'emergency'];

    private const LOG_DIR = __DIR__ . '/../../Resources'; // adjust if needed

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
        $text = "[{$timestamp}] [{$type}] {$message}";

        // Output to Console
        echo "{$color}{$text}" . self::RESET . "\n";

        // Also write to file
        self::writeToFile($text);
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

    private static function writeToFile(string $text): void
    {
        // Ensure log directory exists
        if (!is_dir(self::LOG_DIR)) {
            mkdir(self::LOG_DIR, 0777, true);
        }

        // Daily log file name (can change to a single file if preferred)
        $date = date('Y-m-d');
        $logFile = self::LOG_DIR . "/log-{$date}.txt";
        $latestLog = self::LOG_DIR . "/latest.log";

        // Append the log entry
        file_put_contents($logFile, $text . PHP_EOL, FILE_APPEND);

        // Update latest.log
        file_put_contents($latestLog, $text . PHP_EOL, FILE_APPEND);
    }
}
?>
