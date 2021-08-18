<?php

use app\helpers\Html;

/** @var $dataProvider \yii\data\ActiveDataProvider */
/** @var $this \app\base\View */
/** @var $currentBalance integer */
/** @var $userPremium \app\models\UserPremium */
/** @var $userBoost \app\models\UserBoost */
/** @var $boostPrice integer */
/** @var $boostDuration integer */
/** @var $premiumPrice integer */
/** @var $premiumDuration integer */
/** @var $premiumSettings \app\forms\PremiumSettingsForm */

$this->title = Yii::t('youdate', 'Services');
?>

<?php $this->beginContent('@theme/views/balance/_layout.php', [
    'currentBalance' => $currentBalance,
    'showAddButton' => true,
]) ?>


<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <a name="premium"><?= Yii::t('youdate', 'Premium account') ?></a>
        </h3>
    </div>
    <div class="card-body">
        <?php if ($message = Yii::$app->session->getFlash('user-premium')): ?>
            <div class="alert alert-success alert-dismissible">
                <button type="button" class="close" data-dismiss="alert"></button>
                <?= Html::encode($message) ?>
            </div>
        <?php endif; ?>
        <?php $form = \youdate\widgets\ActiveForm::begin(['action' => ['activate-premium'], 'method' => 'post']) ?>
            <div class="feature d-flex flex-column align-items-center flex-sm-row">
                <div class="feature-icon bg-orange d-flex justify-content-center align-items-center mr-3 mb-4 mb-sm-0">
                    <i class="fa fa-diamond"></i>
                </div>
                <div class="feature-title">
                    <?php if ($userPremium && $userPremium->isPremium): ?>
                        <?= Yii::t('youdate', 'Premium features active until') ?>
                        &mdash;
                        <span class="text-muted"><?=  Yii::$app->formatter->asDate($userPremium->premium_until) ?></span>
                    <?php else: ?>
                        <?= Yii::t('youdate', 'Premium status') ?>
                        &mdash;
                        <span class="text-muted"><?= Yii::t('youdate', 'off') ?></span>
                    <?php endif; ?>
                    <div class="feature-price">
                        <?= Yii::t('youdate', '{credits} credits for {days} days', [
                            'credits' => '<strong>' . $premiumPrice . '</strong>',
                            'days' => '<strong>' . $premiumDuration . '</strong>',
                        ]) ?>
                    </div>
                </div>
                <div class="feature-status ml-0 mt-2 ml-sm-auto mt-sm-0">
                    <?php if ($userPremium && $userPremium->isPremium): ?>
                        <button class="btn btn-outline-primary btn-disabled" disabled="disabled">
                            <i class="fa fa-check-circle pr-2"></i><?= Yii::t('youdate', 'Activated') ?>
                        </button>
                    <?php else: ?>
                        <?= Html::button('<i class="fa fa-check-circle pr-2"></i>'
                            . Yii::t('youdate', 'Activate'), [
                            'class' => 'btn btn-primary btn-md',
                            'type' => 'submit',
                        ]) ?>
                    <?php endif; ?>
                </div>
            </div>
        <?php \youdate\widgets\ActiveForm::end() ?>
        <?php if ($userPremium && $userPremium->isPremium): ?>
            <?= $this->render('_premium_settings', ['premiumSettings' => $premiumSettings]) ?>
        <?php else: ?>
            <?= $this->render('_premium_about') ?>
        <?php endif; ?>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <a name="rise-up"><?= Yii::t('youdate', 'Rise up in search') ?></a>
        </h3>
    </div>
    <div class="card-body">
        <?php if ($message = Yii::$app->session->getFlash('user-boost')): ?>
            <div class="alert alert-success alert-dismissible">
                <button type="button" class="close" data-dismiss="alert"></button>
                <?= Html::encode($message) ?>
            </div>
        <?php endif; ?>
        <?php $form = \youdate\widgets\ActiveForm::begin(['action' => ['rise-up'], 'method' => 'post']) ?>
        <div class="feature d-flex flex-column align-items-center flex-sm-row">
            <div class="feature-icon bg-green d-flex justify-content-center align-items-center mr-3 mb-4 mb-sm-0">
                <i class="fa fa-arrow-up"></i>
            </div>
            <div class="feature-title text-center text-sm-left">
                <?php if ($userBoost == null): ?>
                    <?= Yii::t('youdate', 'You have no active rises in search at the moment') ?>
                <?php else: ?>
                    <?= Yii::t('youdate', 'Your profile was raised up until {date}', [
                        'date' => ' &mdash; <span class="text-muted">' . Yii::$app->formatter->asDate($userBoost->boosted_until) . '</span>',
                    ]) ?>
                <?php endif; ?>
                <div class="feature-price">
                    <?= Yii::t('youdate', '{credits} credits for {days} days', [
                        'credits' => '<strong>' . $boostPrice . '</strong>',
                        'days' => '<strong>' . $boostDuration . '</strong>',
                    ]) ?>
                </div>
            </div>
            <div class="feature-status ml-0 mt-2 ml-sm-auto mt-sm-0">
                <?= Html::button('<i class="fa fa-arrow-up pr-2"></i>' . Yii::t('youdate', 'Rise up now'), [
                    'class' => 'btn btn-primary btn-md d-block d-sm-inline-block',
                    'type' => 'submit',
                ]) ?>
            </div>
        </div>
        <?php \youdate\widgets\ActiveForm::end() ?>
    </div>
</div>



<?php $this->endContent() ?>
