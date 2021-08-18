<?php

namespace app\payments;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package app\payments
 */
abstract class TransactionInfo
{
    const TYPE_PAYMENT = 'payment';
    const TYPE_GIFT = 'gift';
    const TYPE_PREMIUM = 'premium';
    const TYPE_BOOST = 'boost';
    const TYPE_SPOTLIGHT = 'spotlight';

    /**
     * @return string
     */
    abstract public function getType();
    /**
     * @return string
     */
    abstract public function getTitle();
    /**
     * @return string
     */
    abstract public function getServiceName();

    /**
     * @param $data
     */
    public function setData($data)
    {
        if (!is_array($data)) {
            return;
        }

        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
    }
}
