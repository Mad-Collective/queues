<?php

namespace Infrastructure\Logger;

class NaiveStdoutLogger implements \Psr\Log\LoggerInterface
{

    public function emergency($message, array $context = [])
    {
        echo '<emergency>:' . $message . PHP_EOL;
    }

    public function alert($message, array $context = [])
    {
        echo '<alert>:' . $message . PHP_EOL;
    }

    public function critical($message, array $context = [])
    {
        echo '<critical>:' . $message . PHP_EOL;
    }

    public function error($message, array $context = [])
    {
        echo '<error>:' . $message . PHP_EOL;
    }

    public function warning($message, array $context = [])
    {
        echo '<warning>:' . $message . PHP_EOL;
    }

    public function notice($message, array $context = [])
    {
        echo '<notice>:' . $message . PHP_EOL;
    }

    public function info($message, array $context = [])
    {
        echo '<info>:' . $message . PHP_EOL;
    }

    public function debug($message, array $context = [])
    {
        echo '<debug>:' . $message . PHP_EOL;
    }

    public function log($level, $message, array $context = [])
    {
        echo '<log>:' . $message . PHP_EOL;
    }
}