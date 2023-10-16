<?php

namespace Cmp\Queues\Infrastructure\Logger;

class NaiveStdoutLogger implements \Psr\Log\LoggerInterface
{

    public function emergency($message, array $context = []): void
    {
        echo '<emergency>:' . $message . PHP_EOL;
    }

    public function alert($message, array $context = []): void
    {
        echo '<alert>:' . $message . PHP_EOL;
    }

    public function critical($message, array $context = []): void
    {
        echo '<critical>:' . $message . PHP_EOL;
    }

    public function error($message, array $context = []): void
    {
        echo '<error>:' . $message . PHP_EOL;
    }

    public function warning($message, array $context = []): void
    {
        echo '<warning>:' . $message . PHP_EOL;
    }

    public function notice($message, array $context = []): void
    {
        echo '<notice>:' . $message . PHP_EOL;
    }

    public function info($message, array $context = []): void
    {
        echo '<info>:' . $message . PHP_EOL;
    }

    public function debug($message, array $context = []): void
    {
        echo '<debug>:' . $message . PHP_EOL;
    }

    public function log($level, $message, array $context = []): void
    {
        echo '<log>:' . $message . PHP_EOL;
    }
}
