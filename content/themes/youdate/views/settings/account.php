<?php

use yii\helpers\Html;
use youdate\widgets\ActiveForm;

/** @var $this \app\base\View */
/** @var $form \yii\widgets\ActiveForm */
/** @var $model \app\forms\SettingsForm */

$this->title = Yii::t('youdate', 'Account settings');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="card">
    <div class="card-header">
        <h3 class="card-title"><?= Yii::t('youdate', 'Account settings') ?></h3>
    </div>
    <div class="card-body">
        <?= $this->render('/_alert', ['module' => Yii::$app->getModule('user')]) ?>
        <?php $form = ActiveForm::begin([
            'id' => 'account-form',
            'enableAjaxValidation' => true,
            'enableClientValidation' => true,
        ]); ?>
        <div class="row">
            <div class="col-sm-6">
                <?= $form->field($model, 'email') ?>
            </div>
            <div class="col-sm-6">
                <?= $form->field($model, 'newPassword')->passwordInput() ?>
            </div>
        </div>
        <?= $form->field($model, 'currentPassword')->passwordInput() ?>
        <?= Html::submitButton(Yii::t('youdate', 'Save'), ['class' => 'btn float-right btn-primary']) ?><br>
        <?php ActiveForm::end(); ?>
    </div>
</div>
