<?php

namespace app\payments;

use app\managers\BalanceManager;
use app\models\PaymentCustomer;
use Stripe\Stripe;
use Yii;

class StripeCheckout extends Checkout
{
    /**
     * @var string
     */
    protected $stripeSecretKey;
    /**
     * @var string
     */
    protected $stripeToken;
    /**
     * @var string
     */
    protected $service = 'stripe';

    /**
     * @param $key
     */
    public function setSecretKey($key)
    {
        $this->stripeSecretKey = $key;
    }

    /**
     * @param $token
     */
    public function setToken($token)
    {
        $this->stripeToken = $token;
    }

    /**
     * @return bool
     * @throws \Exception
     */
    public function checkout()
    {
        try {
            Stripe::setApiKey($this->stripeSecretKey);
            $customerId = $this->getCustomerId();
            $charge = \Stripe\Charge::create(array(
                'customer' => $customerId,
                'amount' => $this->getAmount(),
                'currency' => $this->currency,
            ));

        } catch (\Exception $e) {
            Yii::$app->session->setFlash('danger', $e->getMessage());
            return false;
        }

        /** @var BalanceManager $balanceManager */
        $balanceManager = Yii::$app->balanceManager;
        $balanceManager->increase(['user_id' => $this->user->id], $this->credits, [
            'class' => StripeTransaction::class,
            'stripeData' => $charge,
        ]);

        Yii::$app->session->setFlash('success',
            Yii::t('app', 'Added {0} credits to your balance', $this->credits)
        );

        return true;
    }


    /**
     * @return float|integer
     */
    protected function getAmount()
    {
        return ($this->credits / $this->rate) * 100;
    }

    /**
     * @return string
     */
    protected function getCustomerId()
    {
        $paymentCustomer = PaymentCustomer::findOne(['user_id' => $this->user->id, 'service' => $this->service]);
        if ($paymentCustomer) {
            $stripeCustomerInfo = json_decode($paymentCustomer->data);
            if (sha1($this->stripeToken) !== $stripeCustomerInfo->stripeToken) {
                \Stripe\Customer::update($stripeCustomerInfo->id, ['source' => $this->stripeToken]);
            }
            return $stripeCustomerInfo->id;
        }

        $customer = \Stripe\Customer::create(['email' => $this->user->email, 'source' => $this->stripeToken]);
        $paymentCustomer = new PaymentCustomer();
        $paymentCustomer->user_id = $this->user->id;
        $paymentCustomer->service = $this->service;
        $paymentCustomer->data = json_encode(['id' => $customer->id, 'stripeToken' => sha1($this->stripeToken)]);
        $paymentCustomer->save();

        return $customer->id;
    }
}
