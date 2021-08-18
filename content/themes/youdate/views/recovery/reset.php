<?php

use app\helpers\Html;
use youdate\widgets\ActiveForm;

/** @var \yii\web\View $this **/
/** @var yii\widgets\ActiveForm $form **/
/** @var \app\forms\RecoveryForm $model */

$this->title = Yii::t('youdate', 'Reset your password');
$this->context->layout = '//page-single';
?>
<div class="col-login mx-auto">
    <?= $this->render('/partials/auth-header.php') ?>
    <div class="card">
        <div class="card-body">
            <div class="card-title">
                <?= Html::encode($this->title) ?>
            </div>
            <?php $form = ActiveForm::begin([
                'id' => 'password-recovery-form',
                'enableAjaxValidation' => true,
                'enableClientValidation' => false,
            ]); ?>
            <?= $form->field($model, 'password')->passwordInput() ?>
            <?= Html::submitButton(Yii::t('youdate', 'Finish'), ['class' => 'btn btn-primary btn-block']) ?><br>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
