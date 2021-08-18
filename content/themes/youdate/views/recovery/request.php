<?php

use app\helpers\Html;
use youdate\widgets\ActiveForm;

/** @var \yii\web\View $this **/
/** @var yii\widgets\ActiveForm $form **/
/** @var \app\forms\RecoveryForm $model */

$this->title = Yii::t('youdate', 'Recover your password');
$this->context->layout = '//page-single';
?>
<div class="col-recovery mx-auto">
    <?= $this->render('/partials/auth-header.php') ?>
    <div class="card">
        <div class="card-body">
            <div class="card-title">
                <?= Html::encode($this->title) ?>
            </div>
            <?php $form = ActiveForm::begin([
                'id' => 'password-recovery-form',
                'enableAjaxValidation' => false,
                'enableClientValidation' => true,
            ]); ?>
            <?= $form->field($model, 'email')->textInput(['autofocus' => true]) ?>
            <?php if ($model->isCaptchaRequired()): ?>
                <?= $form->field($model, 'captcha')->widget(\yii\captcha\Captcha::class) ?>
            <?php endif; ?>
            <?= Html::submitButton(Yii::t('youdate', 'Continue'), ['class' => 'btn btn-primary btn-block']) ?>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
