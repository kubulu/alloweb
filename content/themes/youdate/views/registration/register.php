<?php

use app\helpers\Html;
use youdate\widgets\ActiveForm;

/** @var \yii\web\View $this **/
/** @var \app\forms\RegistrationForm $model **/
/** @var array $sexOptions **/
/** @var array $countries **/

$this->title = Yii::t('youdate', 'Sign up');
$this->context->layout = '//page-single';
?>
<div class="col-registration mx-auto">
    <?= $this->render('/partials/auth-header.php') ?>
    <?php $form = ActiveForm::begin([
        'id' => 'registration-form',
        'enableAjaxValidation' => false,
        'enableClientValidation' => true,
        'validateOnBlur' => false,
        'validateOnType' => false,
        'validateOnChange' => false,
        'options' => ['class' => 'card'],
    ]); ?>
    <div class="card-body">
        <div class="card-title">
            <?= Html::encode($this->title) ?>
        </div>
        <?= $form->errorSummary($model) ?>
        <?= $this->render('_register_form', [
            'form' => $form,
            'model' => $model,
            'sexOptions' => $sexOptions,
            'countries' => $countries,
            'account' => null
        ]) ?>
        <?= Html::submitButton(Yii::t('youdate', 'Sign up'), ['class' => 'btn btn-primary btn-block']) ?>
    </div>
    <?php ActiveForm::end(); ?>

    <p class="text-center">
        <?= Html::a(Yii::t('youdate', 'Already registered? Sign in!'), ['/security/login']) ?>
    </p>
</div>
