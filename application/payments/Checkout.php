<?php

namespace app\payments;

use app\models\User;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package app\payments
 */
abstract class Checkout
{
    /**
     * @var User
     */
    protected $user;
    /**
     * @var integer
     */
    protected $credits;
    /**
     * @var float
     */
    protected $rate;
    /**
     * @var string
     */
    protected $currency;

    /**
     * @return float|integer
     */
    abstract protected function getAmount();

    /**
     * @return mixed
     */
    abstract public function checkout();

    /**
     * @param $user User
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

    /**
     * @param $credits
     */
    public function setCredits($credits)
    {
        $this->credits = $credits;
    }

    /**
     * @param $rate
     */
    public function setRate($rate)
    {
        $this->rate = $rate;
    }

    /**
     * @param $currency
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;
    }
}
