<?php

namespace Cmp\Queues\Domain\Queue;

interface Message extends \JsonSerializable
{
    /**
     * @return string
     */
    public function getName();

    /**
     * @return array
     */
    public function getBody();

    /**
     * @return int
     */
    public function getDelay();
}