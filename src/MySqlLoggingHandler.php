<?php

declare(strict_types=1);

namespace Ravisdo\Bot;

use Monolog\Logger;
use Monolog\Handler\AbstractProcessingHandler;
use Ravisdo\Bot\Models\BotLogEntry;

class MySqlLoggingHandler extends AbstractProcessingHandler
{
    public function __construct(int $level = Logger::DEBUG, bool $bubble = true)
    {
        parent::__construct($level, $bubble);
    }

    protected function write(array $record): void
    {
        $logEntry = new BotLogEntry();
        $logEntry->message = $record['message'];
        $logEntry->context = json_encode($record['context']);
        $logEntry->level = $record['level'];
        $logEntry->level_name = $record['level_name'];
        $logEntry->channel = $record['channel'];
        $logEntry->record_datetime = $record['datetime']->format('Y-m-d H:i:s');
        $logEntry->formatted = $record['formatted'];
        $logEntry->extra = json_encode($record['extra']);
        $logEntry->save();
        if ($this->level === Logger::DEBUG) {
            echo $record['formatted'];
        }
    }
}
