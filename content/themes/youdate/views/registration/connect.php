<?php

use app\helpers\Html;
use youdate\widgets\ActiveForm;

/** @var \app\base\View $this */
/** @var ActiveForm $form */
/** @var \app\forms\RegistrationForm $models */
/** @var \app\models\Account $account */
/** @var array $sexOptions **/
/** @var array $countries **/

$this->title = Yii::t('youdate', 'Sign in');
$this->context->layout = '//page-single';
?>
<div class="col-registration mx-auto">
    <?= $this->render('/partials/auth-header.php') ?>
    <div class="card">
        <?php $form = ActiveForm::begin([
            'id' => 'connect-form',
            'enableAjaxValidation' => false,
            'enableClientValidation' => true,
            'validateOnBlur' => false,
            'validateOnType' => false,
            'validateOnChange' => false,
        ]); ?>
        <div class="card-body">
            <div class="card-title">
                <?= Html::encode($this->title) ?>
            </div>
            <div class="alert alert-info">
                <?= Yii::t('youdate',
                    'In order to finish your registration, we need you to enter following fields'
                ) ?>:
            </div>
            <?= $this->render('_register_form', [
                'form' => $form,
                'model' => $model,
                'sexOptions' => $sexOptions,
                'countries' => $countries,
                'account' => $account,
            ]) ?>
            <?= Html::submitButton(Yii::t('youdate', 'Continue'), ['class' => 'btn btn-primary btn-block']) ?>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
    <p class="text-center">
        <?= Html::a(Yii::t('youdate', 'If you already registered, sign in and connect this account on settings page'), ['/settings/networks']) ?>.
    </p>
</div>
