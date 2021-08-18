<?php

use app\helpers\Html;
use app\helpers\Url;

/** @var $this \app\base\View */
/** @var $currentBalance integer */
/** @var $stripePublishableKey string */
/** @var $rate string */
/** @var $currency \app\models\Currency */
/** @var $creditVariants array */
/** @var $siteName string */
/** @var $userEmail string */
/** @var $stripeEnabled boolean */
/** @var $paypalEnabled boolean */

$this->registerAssetBundle(youdate\assets\PaymentAsset::class);
$this->title = Yii::t('youdate', 'Buy credits');
?>

<?php $this->beginContent('@theme/views/balance/_layout.php', ['currentBalance' => $currentBalance]) ?>

<div class="card ">
    <div class="card-header">
        <h3 class="card-title">
            <?= Yii::t('youdate', 'Buy credits') ?>
        </h3>
    </div>
    <div class="card-body">
        <div class="payment-add">
            <?php $form = \youdate\widgets\ActiveForm::begin([
                'method' => 'post',
                'options' => [
                    'class' => 'payment-form',
                    'data-action-stripe' => Url::to(['process-stripe'], true),
                    'data-action-paypal' => Url::to(['process-paypal'], true),
                ],
            ]) ?>

            <?= $this->render('/_alert') ?>

            <div class="custom-controls-stacked mb-5">
                <div class="row">
                    <?php foreach ($creditVariants as $credits => $value): ?>
                        <div class="col-6">
                            <label class="custom-control custom-radio payment-amount-variant">
                                <input type="radio" class="custom-control-input credits-input"
                                       name="credits"
                                       checked="checked"
                                       data-amount="<?= $value ?>"
                                       value="<?= $credits ?>">
                                <span class="custom-control-label credits-count">
                        <span class="credits-count">
                            <?= Yii::t('youdate', '{count} credits', ['count' => $credits]) ?>
                        </span>
                        <span class="credits-amount">
                            <?= Yii::t('youdate', 'for {amount} {currency}', [
                                'amount' => sprintf(Html::encode($currency->format), $value),
                                'currency' => '',
                            ]) ?>
                        </span>
                    </span>
                            </label>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <?php if ($stripeEnabled): ?>
                <button type="submit"
                        data-key="<?= $stripePublishableKey ?>"
                        data-currency="<?= $currency ?>"
                        data-name="<?= Html::encode($siteName) ?>"
                        data-description="<?= Yii::t('youdate', 'Credits purchase') ?>: "
                        data-color="white"
                        data-email="<?= Html::encode($userEmail) ?>"
                        data-image="<?= Url::to(['@themeUrl/static/images/logo-stripe.png'], true) ?>"
                        class="btn btn-outline-primary btn-stripe btn-lg mb-3 mb-md-0">
                    <i class="fa fa-cc-stripe mr-2"></i><?= Yii::t('youdate', 'Pay with Stripe') ?>
                </button>
            <?php endif; ?>

            <?php if ($paypalEnabled): ?>
                <button type="submit"
                        class="btn btn-outline-primary btn-paypal btn-lg mb-3 mb-md-0">
                    <i class="fa fa-cc-paypal mr-2"></i><?= Yii::t('youdate', 'Pay with PayPal') ?>
                </button>
            <?php endif; ?>

            <?php \youdate\widgets\ActiveForm::end() ?>
        </div>
        <div class="payment-loader hidden pt-5 pb-5 mt-5 mb-5">
            <div class="dimmer active">
                <div class="loader"></div>
                <div class="dimmer-content">
                    <div class="text-muted text-center"><?= Yii::t('youdate', 'Please wait...') ?></div>
                </div>
            </div>

        </div>
    </div>
</div>

<?php $this->endContent() ?>
