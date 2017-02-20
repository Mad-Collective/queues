<?php
/**
 * Created by PhpStorm.
 * User: quimmanrique
 * Date: 13/02/17
 * Time: 13:40
 */

namespace Domain\Queue;

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