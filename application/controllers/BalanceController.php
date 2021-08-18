<?php

namespace app\controllers;

use app\forms\PaypalPaymentForm;
use app\forms\PremiumSettingsForm;
use app\forms\SpotlightForm;
use app\forms\StripePaymentForm;
use app\models\Currency;
use app\payments\CheckoutHelper;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package app\controllers
 */
class BalanceController extends \app\base\Controller
{
    /**
     * @var string
     */
    public $defaultAction = 'services';

    /**
     * @return array
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'process-stripe' => ['post'],
                    'process-paypal' => ['post'],
                    'activate-premium' => ['post'],
                    'rise-up' => ['post'],
                    'spotlight-submit' => ['post'],
                ],
            ],
        ];
    }

    /**
     * @param $action
     * @return bool
     * @throws NotFoundHttpException
     * @throws \yii\web\BadRequestHttpException
     */
    public function beforeAction($action)
    {
        if (!$this->balanceManager->isPremiumFeaturesEnabled()) {
            throw new NotFoundHttpException();
        }

        return parent::beforeAction($action);
    }

    /**
     * @return string
     */
    public function actionServices()
    {
        $user = $this->getCurrentUser();

        return $this->render('services', [
            'currentBalance' => $this->balanceManager->getUserBalance($user->id),
            'boostPrice' => $this->balanceManager->getBoostPrice(),
            'boostDuration' => $this->balanceManager->getBoostDuration(),
            'premiumPrice' => $this->balanceManager->getPremiumPrice(),
            'premiumDuration' => $this->balanceManager->getPremiumDuration(),
            'userBoost' => $user->boost,
            'userPremium' => $user->premium,
            'premiumSettings' => PremiumSettingsForm::fromUserPremium($user->premium),
        ]);
    }

    /**
     * @return string
     */
    public function actionTransactions()
    {
        $userId = Yii::$app->user->id;

        return $this->render('transactions', [
            'currentBalance' => $this->balanceManager->getUserBalance($userId),
            'dataProvider' => $this->balanceManager->getTransactionsProvider($userId),
        ]);
    }

    /**
     * @return string
     */
    public function actionBuy()
    {
        $userId = Yii::$app->user->id;
        $settings = Yii::$app->settings;
        $rate = $settings->get('common', 'paymentRate');
        $currency = Currency::getCurrency($settings->get('common', 'paymentCurrency'));
        $stripePublishableKey = $settings->get('common', 'paymentStripePublishableKey');
        $siteName = $settings->get('frontend', 'siteName');

        return $this->render('buy', [
            'currentBalance' => $this->balanceManager->getUserBalance($userId),
            'rate' => $rate,
            'currency' => $currency,
            'stripePublishableKey' => $stripePublishableKey,
            'userEmail' => $this->getCurrentUser()->email,
            'siteName' => $siteName,
            'creditVariants' => $this->getCreditVariants($rate),
            'stripeEnabled' => $settings->get('common', 'paymentStripeEnabled'),
            'paypalEnabled' => $settings->get('common', 'paymentPaypalEnabled'),
        ]);
    }

    /**
     * @return \yii\web\Response
     * @throws \Exception
     */
    public function actionProcessStripe()
    {
        $form = new StripePaymentForm();
        if (!$form->load(Yii::$app->request->post()) || !$form->validate()) {
            Yii::$app->session->setFlash('danger',
                Yii::t('app', 'Unknown payment error occurred. Please try again later'));
            return $this->redirect(['buy']);
        }

        $checkout = CheckoutHelper::getStripe($this->getCurrentUser(), $form->credits, $form->stripeToken);
        $checkout->checkout();

        return $this->redirect(['buy']);
    }

    /**
     * @return \yii\web\Response
     * @throws \yii\web\HttpException
     * @throws \yii\base\ErrorException
     */
    public function actionProcessPaypal()
    {
        $form = new PaypalPaymentForm();
        if (!$form->load(Yii::$app->request->post()) || !$form->validate()) {
            Yii::$app->session->setFlash('danger',
                Yii::t('app', 'Unknown payment error occurred. Please try again later'));
            return $this->redirect(['buy']);
        }

        $checkout = CheckoutHelper::getPaypal($this->getCurrentUser(), $form->credits);
        $checkout->checkout();

        return null;
    }

    /**
     * @return \yii\web\Response
     * @throws \yii\base\ErrorException
     */
    public function actionPaypalSuccess()
    {
        $invoiceId = Yii::$app->session->get('paypalInvoiceId');
        $paymentId = Yii::$app->request->get('paymentId', null);
        $payerId = Yii::$app->request->get('PayerID', null);

        if (!$paymentId || !$payerId) {
            $this->redirect(['buy']);
        }

        $checkout = CheckoutHelper::getPaypal($this->getCurrentUser(), Yii::$app->session->get('paypalCreditsPurchased'));
        $checkout->validatePayment($paymentId, $payerId, $invoiceId);

        return $this->redirect(['buy']);
    }

    /**
     * @return \yii\web\Response
     */
    public function actionPaypalFailure()
    {
        Yii::$app->session->setFlash('boostedDomainMessage', 'Payment canceled');

        return $this->redirect(['buy']);
    }

    /**
     * @return \yii\web\Response
     */
    public function actionRiseUp()
    {
        $user = $this->getCurrentUser();
        if ($this->balanceManager->boostUser($user->id)) {
            Yii::$app->session->setFlash('user-boost',
                Yii::t('app', 'Your profile has been raised up in search')
            );
        } else {
            Yii::$app->session->setFlash('user-boost',
                Yii::t('app', 'You don\'t have enough credits for this operation')
            );
        }

        return $this->redirect(['services#rise-up']);
    }

    /**
     * @return \yii\web\Response
     */
    public function actionActivatePremium()
    {
        $user = $this->getCurrentUser();
        if ($this->balanceManager->activatePremium($user->id)) {
            Yii::$app->session->setFlash('user-premium',
                Yii::t('app', 'Premium features activated')
            );
        } else {
            Yii::$app->session->setFlash('user-premium',
                Yii::t('app', 'You don\'t have enough credits for this operation')
            );
        }

        return $this->redirect(['services#premium']);
    }

    /**
     * @return \yii\web\Response
     */
    public function actionPremiumSettings()
    {
        $user = $this->getCurrentUser();
        if (!$user->isPremium) {
            return $this->redirect(['services']);
        }

        $form = new PremiumSettingsForm();
        if ($form->load(Yii::$app->request->post()) && $form->validate()) {
            $user->premium->show_online_status = $form->showOnlineStatus;
            $user->premium->incognito_active = $form->incognitoActive;
            $user->premium->save();
            Yii::$app->session->setFlash('user-premium', Yii::t('app', 'Premium settings saved'));
        }

        return $this->redirect(['services']);
    }

    /**
     * @throws \yii\base\ExitException
     * @throws \yii\db\Exception
     */
    public function actionSpotlightSubmit()
    {
        $user = $this->getCurrentUser();
        $form = new SpotlightForm();
        $form->load(Yii::$app->request->post());
        if ($form->photoId == null) {
            $form->photoId = $this->getCurrentUserProfile()->photo_id;
        }

        if ($form->validate()) {
            if ($this->balanceManager->submitSpotlight($user->id, $form->photoId, $form->message)) {
                return $this->sendJson([
                    'success' => true,
                    'message' => Yii::t('app','Photo has been placed on spotlight'),
                    'balance' => $this->balanceManager->getUserBalance(Yii::$app->user->id),
                ]);
            }
        }

        return $this->sendJson([
            'success' => false,
            'errors' => $form->errors,
        ]);
    }

    /**
     * @param $rate
     * @return array
     */
    protected function getCreditVariants($rate)
    {
        $variants = [];
        foreach ([50, 100, 250, 500] as $credits) {
            $variants[$credits] = sprintf('%.2f', $credits / $rate);
        }

        return $variants;
    }
}
