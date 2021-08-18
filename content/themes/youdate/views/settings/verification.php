<?php

use youdate\widgets\ActiveForm;
use app\models\Profile;
use yii\helpers\Html;

/** @var $this \app\base\View */
/** @var $verificationForm \app\forms\VerificationForm */
/** @var $verificationEntry \app\models\Verification */
/** @var $form \yii\widgets\ActiveForm */
/** @var $user \app\models\User */
/** @var $profile \app\models\Profile */

$this->title = Yii::t('youdate', 'Verification');
$this->params['breadcrumbs'][] = $this->title;
$this->registerJsFile('@themeUrl/static/js/verification.js', [
    'depends' => [
        \youdate\assets\Asset::class,
        \youdate\assets\WebCamJsAsset::class,
    ]
]);
if (isset($profile->sex)) {
    if ($profile->sex == Profile::SEX_MALE || $profile->sex == Profile::SEX_NOT_SET) {
        $gesture = 'man';
    } else {
        $gesture = 'woman';
    }
} else {
    $gesture = 'man';
}
$gestureImageUrl = \app\helpers\Url::to(["@themeUrl/static/images/gesture-$gesture.svg"]);
?>
<div class="card">
    <div class="card-header">
        <h3 class="card-title"><?= Html::encode($this->title) ?></h3>
    </div>
    <div class="card-body">
        <?= $this->render('/_alert', ['module' => Yii::$app->getModule('user')]) ?>

        <?php if ($profile->is_verified): ?>
            <?= \youdate\widgets\EmptyState::widget([
                'icon' => 'fe fe-check-circle',
                'title' => Yii::t('youdate', 'Your photo has been verified'),
                'subTitle' => Yii::t('youdate', 'Congratulations! You have "verified" badge for now'),
            ]) ?>
        <?php elseif ($verificationEntry == null || $verificationEntry->is_viewed): ?>

            <?php $form = ActiveForm::begin([
                'id' => 'verification-form',
                'options' => ['class' => 'form-horizontal', 'data-user-id' => $user->id],
                'enableAjaxValidation' => true,
                'enableClientValidation' => true,
                'validateOnBlur' => false,
            ]); ?>

            <?= $form->errorSummary($verificationForm) ?>

            <div class="alert alert-warning alert-webcam hidden">
                <?= Yii::t('youdate', 'Web-camera access is not allowed.') ?>
            </div>

            <?php if (isset($verificationEntry) && $verificationEntry->is_viewed): ?>
                <div class="alert alert-danger">
                    <?= Yii::t('youdate', 'Sorry, your previous photo submission was rejected.') ?>
                </div>
            <?php endif; ?>

            <div class="row">
                <div class="col-sm-12 col-sm-md-12 col-lg-6 col-webcam">
                    <div class="webcam-wrapper">
                        <div class="webcam-camera">
                            <i class="fe fe-camera"></i>
                        </div>
                        <div class="webcam">
                        </div>
                    </div>
                </div>
                <div class="col-sm-12 col-sm-md-12 col-lg-6 d-flex align-content-center justify-content-center flex-column mt-sm-3">
                    <div class="text-center">
                        <div class="verification-gesture mb-2">
                            <img src="<?= $gestureImageUrl ?>" alt="Gesture">
                        </div>
                        <h3><?= Yii::t('youdate', 'Copy this gesture') ?></h3>
                        <div><?= Yii::t('youdate', 'To verify yourself, please match this pose as closely as possible') ?></div>
                        <div class="text-muted text-center mb-3">
                            <small><?= Yii::t('youdate', 'This photo is for moderation only, it will not be published on your profile') ?></small>
                        </div>
                        <?= Html::submitButton(Yii::t('youdate', 'Submit photo'), [
                            'class' => 'btn btn-primary btn-submit',
// todo: remove
//                            'disabled' => true,
                        ]) ?>
                    </div>
                </div>
            </div>
            <?php ActiveForm::end(); ?>
        <?php else: ?>
            <?= \youdate\widgets\EmptyState::widget([
                'icon' => 'fe fe-check-circle',
                'title' => Yii::t('youdate', 'Your photo has been uploaded'),
                'subTitle' => Yii::t('youdate', 'Moderators will verify your submission soon'),
            ]) ?>
        <?php endif; ?>
    </div>
</div>
