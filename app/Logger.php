<?php
/**
 * Logger Class - Handles application logging
 */

class Logger {
    const LEVEL_ERROR = 'ERROR';
    const LEVEL_WARNING = 'WARNING';
    const LEVEL_INFO = 'INFO';
    const LEVEL_DEBUG = 'DEBUG';

    private static $logFile;

    public static function init($logPath) {
        self::$logFile = $logPath . '/app.log';
    }

    public static function error($message, $context = []) {
        self::log(self::LEVEL_ERROR, $message, $context);
    }

    public static function warning($message, $context = []) {
        self::log(self::LEVEL_WARNING, $message, $context);
    }

    public static function info($message, $context = []) {
        self::log(self::LEVEL_INFO, $message, $context);
    }

    public static function debug($message, $context = []) {
        if (APP_DEBUG) {
            self::log(self::LEVEL_DEBUG, $message, $context);
        }
    }

    private static function log($level, $message, $context = []) {
        if (!self::$logFile) {
            return;
        }

        $timestamp = date('Y-m-d H:i:s');
        $contextStr = !empty($context) ? ' | ' . json_encode($context) : '';
        $logMessage = "[$timestamp] [$level] $message$contextStr" . PHP_EOL;

        if (!is_dir(dirname(self::$logFile))) {
            mkdir(dirname(self::$logFile), 0755, true);
        }

        error_log($logMessage, 3, self::$logFile);
    }
}

// Initialize logger
Logger::init(LOG_PATH);
