<?php
/**
 * Created by PhpStorm.
 * User: quimmanrique
 * Date: 13/02/17
 * Time: 17:32
 */

namespace Domain\Queue;

use Domain\Queue\Exception\ReaderException;

interface QueueReader
{
    /**
     * @throws ReaderException
     * @return boolean
     */
    public function read();
}