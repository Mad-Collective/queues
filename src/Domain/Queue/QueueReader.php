<?php
/**
 * Created by PhpStorm.
 * User: quimmanrique
 * Date: 13/02/17
 * Time: 17:32
 */

namespace Domain\Queue;

interface QueueReader
{
    /**
     * @param $callback
     * @return mixed
     */
    public function read($callback);
}