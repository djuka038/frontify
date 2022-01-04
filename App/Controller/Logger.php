<?php

namespace App\Controller;

use Monolog\ErrorHandler;
use Monolog\Handler\StreamHandler;

class Logger extends \Monolog\Logger
{
    private static $log_sys = [];

    public function __construct($key = "app", $config = null)
    {
        parent::__construct($key);

        if (empty($config)) {
            $LOG_PATH = AppConfig::get('LOG_PATH', __DIR__ . '/../../log-files');
            $config = [
                'logFile' => "{$LOG_PATH}/{$key}.log",
                'logLevel' => \Monolog\Logger::DEBUG
            ];
        }

        $this->pushHandler(new StreamHandler($config['logFile'], $config['logLevel']));
    }

    public static function getInstance($key = "app", $config = null)
    {
        if (empty(self:: $log_sys[$key])) {
            self:: $log_sys[$key] = new Logger($key, $config);
        }

        return self:: $log_sys[$key];
    }

    public static function enableSystemLogs()
    {

        $LOG_PATH = AppConfig::get('LOG_PATH', __DIR__ . '/../../log-files');
        // Error Log
        self::$log_sys['error'] = new Logger('errors');
        self:: $log_sys['error']->pushHandler(new StreamHandler("{$LOG_PATH}/errors.log"));
        ErrorHandler::register(self::$log_sys['error']);

        // Request Log
        $data = [
            $_SERVER,
            $_REQUEST,
            trim(file_get_contents("php://input"))
        ];
        self::$log_sys['request'] = new Logger('request');
        self::$log_sys['request']->pushHandler(new StreamHandler("{$LOG_PATH}/request.log"));
        self::$log_sys['request']->info("REQUEST", $data);
    }
}