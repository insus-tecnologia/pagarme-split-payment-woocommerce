<?php

namespace PagarmeSplitPayment\Utilities;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class FileLogger {
    private $log;

    public function __construct($loggerName) {
        $this->log = new Logger($loggerName);
        $this->log->pushHandler(new StreamHandler(__DIR__ . '/../../file.log'));
    }

    public function info($logObject) {
        $this->log->info(var_export($logObject, true));
    }
}