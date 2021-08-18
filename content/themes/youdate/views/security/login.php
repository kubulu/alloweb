<?php

use app\helpers\Html;
use youdate\widgets\ActiveForm;
use youdate\widgets\Connect;

/** @var \app\base\View $this */
/** @var \app\forms\LoginForm $model */

$this->title = Yii::t('youdate', 'Sign in');
$this->context->layout = '//page-single';
$this->params['breadcrumbs'][] = $this->title;
?>

<?= $this->render('/_alert', ['module' => Yii::$app->getModule('user')]) ?>

<div class="col-login mx-auto">
    <?= $this->render('/partials/auth-header.php') ?>
    <?php $form = ActiveForm::begin([
        'id' => 'login-form',
        'enableAjaxValidation' => false,
        'enableClientValidation' => true,
        'validateOnBlur' => false,
        'validateOnType' => false,
        'validateOnChange' => false,
        'options' => ['class' => 'card'],
    ]) ?>
    <div class="card-body">
        <div class="card-title">
            <?= Html::encode($this->title) ?>
        </div>
        <?= $form->field($model, 'login', [
            'inputOptions' => [
                'autofocus' => 'autofocus',
                'class' => 'form-control',
                'tabindex' => '1',
                'placeholder' => Yii::t('youdate', 'E-mail'),
            ]
        ]) ?>
        <?= $form->field($model, 'password', [
            'inputOptions' => [
                'class' => 'form-control',
                'tabindex' => '2',
                'placeholder' => Yii::t('youdate', 'Password'),
            ]])
            ->passwordInput()
            ->label(
                Yii::t('youdate', 'Password')
                . (' (' . Html::a(
                        Yii::t('youdate', 'Forgot password?'),
                        ['/recovery/request'],
                        ['tabindex' => '5']
                    ) . ')')
            ) ?>

        <?php if ($model->isCaptchaRequired()): ?>
            <?= $form->field($model, 'captcha')->widget(\yii\captcha\Captcha::class) ?>
        <?php endif; ?>

        <?= $form->field($model, 'rememberMe')->checkbox(['tabindex' => '3']) ?>

        <?= Connect::widget([
            'baseAuthUrl' => ['/security/auth'],
        ]) ?>

        <?= Html::submitButton(
            Yii::t('youdate', 'Sign in'),
            ['class' => 'btn btn-primary btn-block', 'tabindex' => '4']
        ) ?>
    </div>

    <?php ActiveForm::end(); ?>

    <p class="text-center">
        <?= Html::a(Yii::t('youdate', 'Didn\'t receive confirmation message?'), ['/registration/resend']) ?>
    </p>
    <p class="text-center">
        <?= Html::a(Yii::t('youdate', 'Don\'t have an account? Sign up!'), ['/registration/register']) ?>
    </p>
</div>
