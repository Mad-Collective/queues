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
    public function getName();

    public function getBody();
}