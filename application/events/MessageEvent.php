<?php

namespace app\events;

use app\models\Message;
use app\base\Event;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package app\events
 */
class MessageEvent extends Event
{
    /**
     * @var bool
     */
    public $isValid = true;
    /**
     * @var Message
     */
    public $message;
}
