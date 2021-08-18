<?php

namespace app\components\swiftmailer;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package app\components\swiftmailer
 */
class SimpleMailInvoker implements SwiftMailInvoker
{
    /**
     * Send mail via the mail() function.
     *
     * This method takes the same arguments as PHP mail().
     *
     * @param string $to
     * @param string $subject
     * @param string $body
     * @param string $headers
     * @param string $extraParams
     *
     * @return bool
     */
    public function mail($to, $subject, $body, $headers = null, $extraParams = null)
    {
        if (!ini_get('safe_mode')) {
            return @mail($to, $subject, $body, $headers, $extraParams);
        }

        return @mail($to, $subject, $body, $headers);
    }
}
