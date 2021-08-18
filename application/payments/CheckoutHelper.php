<?php

namespace app\payments;

use Yii;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package app\payments
 */
class CheckoutHelper
{
    /**
     * @param $user
     * @param $credits
     * @param $stripeToken
     * @return StripeCheckout
     */
    public static function getStripe($user, $credits, $stripeToken)
    {
        $settings = Yii::$app->settings;
        $rate = $settings->get('common', 'paymentRate');

        $checkout = new StripeCheckout();
        $checkout->setUser($user);
        $checkout->setCredits($credits);
        $checkout->setRate($rate);
        $checkout->setCurrency($settings->get('common', 'paymentCurrency'));
        $checkout->setSecretKey($settings->get('common', 'paymentStripeSecretKey'));
        $checkout->setToken($stripeToken);

        return $checkout;
    }

    /**
     * @param $user
     * @param $credits
     * @return PaypalCheckout
     */
    public static function getPaypal($user, $credits)
    {
        $settings = Yii::$app->settings;
        $rate = $settings->get('common', 'paymentRate');

        $checkout = new PaypalCheckout();
        $checkout->setUser($user);
        $checkout->setCredits($credits);
        $checkout->setRate($rate);
        $checkout->setCurrency($settings->get('common', 'paymentCurrency'));
        $checkout->setClientId($settings->get('common', 'paymentPaypalClientId'));
        $checkout->setClientSecret($settings->get('common', 'paymentPaypalClientSecret'));
        $checkout->setMode( (bool) $settings->get('common', 'paymentPaypalSandbox')
            ? PaypalCheckout::MODE_SANDBOX : PaypalCheckout::MODE_LIVE);

        return $checkout;
    }
}
