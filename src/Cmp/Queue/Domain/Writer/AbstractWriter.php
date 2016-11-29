<?php

namespace Cmp\Queue\Domain\Writer;


use Cmp\Queue\Domain\Message\Message;

abstract class AbstractWriter
{

    private $messages = [];

    /**
     * @param Message $message
     *
     * @return mixed
     */
    abstract protected function writeOne(Message $message);

    /**
     * @param Message[] $messages
     *
     * @return mixed
     */
    abstract protected function writeSome(array $messages);

    /**
     * @param Message $message
     */
    public function add(Message $message)
    {
        array_push($this->messages, $message);
    }

    /**
     * @throws \Cmp\Queue\Domain\ConnectionException
     */
    public function write()
    {
        $numOfDomainObjects = count($this->messages);

        if ($numOfDomainObjects === 1) {
            $this->writeOne($this->messages[0]);
        } else if($numOfDomainObjects > 1) {
            $this->writeSome($this->messages);
        }
        $this->messages = [];
    }

}