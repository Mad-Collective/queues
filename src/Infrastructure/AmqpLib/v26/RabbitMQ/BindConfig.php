<?php
/**
 * Created by PhpStorm.
 * User: quimmanrique
 * Date: 13/02/17
 * Time: 16:13
 */

namespace Infrastructure\AmqpLib\v26;


class BindConfig
{
    protected $topics = [];

    public function addTopic($topic)
    {
        $this->topics[] = $topic;
        return $this;
    }

    public function getTopics()
    {
        return $this->topics;
    }
}