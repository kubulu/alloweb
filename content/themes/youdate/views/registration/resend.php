<?php

use app\helpers\Html;
use youdate\widgets\ActiveForm;

/** @var app\base\View $this */
/** @var \app\forms\ResendForm $model */

$this->title = Yii::t('youdate', 'Request new confirmation message');
$this->context->layout = '//page-single';
?>
<div class="col-resend mx-auto">
    <?= $this->render('/partials/auth-header.php') ?>
    <div class="card">
        <div class="card-body">
            <div class="card-title">
                <?= Html::encode($this->title) ?>
            </div>
            <?php $form = ActiveForm::begin([
                'id' => 'resend-form',
                'enableAjaxValidation' => false,
                'enableClientValidation' => true,
            ]); ?>
            <?= $form->field($model, 'email')->textInput(['autofocus' => true]) ?>
            <?= $form->field($model, 'captcha')->widget(\yii\captcha\Captcha::class) ?>
            <?= Html::submitButton(Yii::t('youdate', 'Resend'), ['class' => 'btn btn-primary btn-block']) ?><br>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
