<?php

namespace app\payments;

use app\helpers\Url;
use app\managers\BalanceManager;
use PayPal\Api\Amount;
use PayPal\Api\Item;
use PayPal\Api\ItemList;
use PayPal\Api\Payer;
use PayPal\Api\Payment;
use PayPal\Api\PaymentExecution;
use PayPal\Api\RedirectUrls;
use PayPal\Api\Transaction;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Rest\ApiContext;
use Yii;
use yii\base\ErrorException;
use yii\helpers\ArrayHelper;
use yii\web\HttpException;

/**
 * @package app\components
 *
 * @property string $clientId
 * @property string $clientSecret
 * @property string $mode
 * @property array $config
 */
class PaypalCheckout extends Checkout
{
    const MODE_SANDBOX = 'sandbox';
    const MODE_LIVE = 'live';

    const LOG_LEVEL_FINE = 'FINE';
    const LOG_LEVEL_INFO = 'INFO';
    const LOG_LEVEL_WARN = 'WARN';
    const LOG_LEVEL_ERROR = 'ERROR';

    /**
     * @var string
     */
    public $clientId;
    /**
     * @var string
     */
    public $clientSecret;
    /**
     * @var string
     */
    public $mode = self::MODE_SANDBOX;
    /**
     * @var array
     */
    public $config = [];
    /**
     * @var ApiContext
     */
    private $_apiContext;

    /**
     * @param $clientId
     */
    public function setClientId($clientId)
    {
        $this->clientId = $clientId;
    }

    /**
     * @param $clientSecret
     */
    public function setClientSecret($clientSecret)
    {
        $this->clientSecret = $clientSecret;
    }

    /**
     * @param $mode
     */
    public function setMode($mode)
    {
        $this->mode = $mode;
    }

    public function initializePaypal()
    {
        $this->_apiContext = new ApiContext(new OAuthTokenCredential($this->clientId, $this->clientSecret));

        $this->_apiContext->setConfig(ArrayHelper::merge([
            'mode' => $this->mode,
            'http.ConnectionTimeOut' => 10,
            'http.Retry' => 1,
            'log.LogEnabled' => env('APP_DEBUG'),
            'log.FileName' => Yii::getAlias('@runtime/logs/paypal.log'),
            'log.LogLevel' => self::LOG_LEVEL_FINE,
            'validation.level' => 'log',
            'cache.enabled' => false
        ], $this->config));

        if (isset($this->config['log.FileName'])
            && isset($this->config['log.LogEnabled'])
            && ((bool)$this->config['log.LogEnabled'] == true)
        ) {
            $logFileName = Yii::getAlias($this->config['log.FileName']);
            if ($logFileName) {
                if (!file_exists($logFileName)) {
                    if (!touch($logFileName)) {
                        throw new ErrorException('Can\'t create paypal.log file at: ' . $logFileName);
                    }
                }
            }
            $this->config['log.FileName'] = $logFileName;
        }
    }

    /**
     * @return mixed|void
     * @throws ErrorException
     * @throws HttpException
     */
    public function checkout()
    {
        $this->initializePaypal();
        $invoiceId = uniqid();
        $payer = new Payer();
        $payer->setPaymentMethod('paypal');

        $amount = new Amount();
        $amount->setCurrency($this->currency)
            ->setTotal($this->getAmount());

        $item = new Item();
        $item->setName(Yii::t('app', 'Credits'))
            ->setCurrency($this->currency)
            ->setQuantity(1)
            ->setPrice($this->getAmount());

        $itemList = new ItemList();
        $itemList->setItems([$item]);

        $transaction = new Transaction();
        $transaction->setAmount($amount)
            ->setDescription(Yii::t('app', 'Credits'))
            ->setInvoiceNumber($invoiceId)
            ->setItemList($itemList);

        $redirectUrls = new RedirectUrls();
        $redirectUrls->setReturnUrl(Url::to(['/balance/paypal-success'], true))
            ->setCancelUrl(Url::to(['/balance/paypal-failure'], true));

        $payment = new Payment();
        $payment->setIntent('sale')
            ->setPayer($payer)
            ->setRedirectUrls($redirectUrls)
            ->setTransactions([$transaction]);

        try {
            Yii::$app->session->set('paypalInvoiceId', $invoiceId);
            Yii::$app->session->set('paypalCreditsPurchased', $this->credits);
            $payment->create($this->getContext());
            Yii::$app->response->redirect($payment->getApprovalLink());
        } catch (\Exception $e) {
            Yii::error($e->getMessage() . ', ' . $e->getFile() . ':' . $e->getLine());
            throw new HttpException(500, 'Could not create payment');
        }
    }

    /**
     * @param $paymentId
     * @param $payerId
     * @param $invoiceId
     * @return bool
     * @throws ErrorException
     */
    public function validatePayment($paymentId, $payerId, $invoiceId)
    {
        $this->initializePaypal();
        $payment = Payment::get($paymentId, $this->getContext());
        $execution = new PaymentExecution();
        $execution->setPayerId($payerId);
        try {
            $payment->execute($execution, $this->getContext());
            try {
                $payment = Payment::get($paymentId, $this->getContext());
                $receivedInvoiceId = $payment->transactions[0]->invoice_number;
                if ($invoiceId != $receivedInvoiceId) {
                    throw new HttpException(400, 'Bad request.');
                }

                /** @var BalanceManager $balanceManager */
                $balanceManager = Yii::$app->balanceManager;
                $balanceManager->increase(['user_id' => $this->user->id], $this->credits, [
                    'class' => PayPalTransaction::class,
                    'paypalInvoiceId' => $invoiceId,
                    'paymentId' => $paymentId,
                    'payerId' => $payerId,
                ]);

                Yii::$app->session->setFlash('success',
                    Yii::t('app', 'Added {0} credits to your balance', $this->credits)
                );

                return true;
            } catch (\Exception $e) {
                Yii::$app->session->setFlash('error', 'Could not get payment info - ' . $e->getMessage());
                return false;
            }
        } catch (\Exception $e) {
            Yii::$app->session->setFlash('error', 'Could not process payment - ' . $e->getMessage());
            return false;
        }
    }

    /**
     * @return ApiContext
     */
    public function getContext()
    {
        return $this->_apiContext;
    }

    /**
     * @return float|integer
     */
    protected function getAmount()
    {
        return sprintf('%.2f', $this->credits / $this->rate);
    }
}
