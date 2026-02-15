<?php
namespace TerrariZ\Utils;

class CrashHandler
{
    public static function register(): void
    {
        set_exception_handler([self::class, 'handleException']);
        set_error_handler([self::class, 'handleError']);
        register_shutdown_function([self::class, 'handleShutdown']);
    }

    public static function handleException(\Throwable $e): void
    {
        Logger::log("emergency", "=== UNCAUGHT EXCEPTION ===");
        Logger::log("emergency", $e->getMessage());
        Logger::log("emergency", $e->getFile() . ":" . $e->getLine());
        Logger::log("emergency", $e->getTraceAsString());
    }

    public static function handleError(int $severity, string $message, string $file, int $line): bool
    {
        Logger::log("error", "PHP ERROR: $message ($file:$line)");
        return true;
    }

    public static function handleShutdown(): void
    {
        $err = error_get_last();
        if ($err !== null) {
            Logger::log("emergency", "=== FATAL SHUTDOWN ===");
            Logger::log("emergency", $err['message']);
            Logger::log("emergency", $err['file'] . ":" . $err['line']);
        }
    }
}
