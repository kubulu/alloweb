<?php

namespace app\components\swiftmailer;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package app\components\swiftmailer
 */
class SwiftMail extends SwiftMailTransport
{
    /**
     * SwiftMail constructor.
     * @param string $extraParams
     */
    public function __construct($extraParams = '-f%s')
    {
        call_user_func_array([$this, 'app\components\swiftmailer\SwiftMailTransport::__construct'], [
                new SimpleMailInvoker(),
                new \Swift_Events_SimpleEventDispatcher(),
        ]);
        $this->setExtraParams($extraParams);
    }

    /**
     * @param string $extraParams
     * @return SwiftMail
     */
    public static function newInstance($extraParams = '-f%s')
    {
        return new self($extraParams);
    }
}
