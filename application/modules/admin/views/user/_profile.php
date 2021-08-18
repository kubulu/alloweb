<?php

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

/** @var $this \yii\web\View */
/** @var $user \app\models\User */
/** @var $profile \app\models\Profile */
?>

<?php $this->beginContent('@app/modules/admin/views/user/update.php', ['user' => $user]) ?>

<div class="box box-default">
    <div class="box-header with-border">
        <h2 class="box-title"><?= Yii::t('app', 'Edit profile') ?></h2>
    </div>
    <div class="box-body">
        <?php $form = ActiveForm::begin([
            'layout' => 'horizontal',
            'enableAjaxValidation' => true,
            'enableClientValidation' => false,
            'fieldConfig' => [
                'horizontalCssClasses' => [
                    'wrapper' => 'col-sm-9',
                ],
            ],
        ]); ?>

        <?= $form->field($profile, 'name') ?>
        <?= $form->field($profile, 'description')->textarea() ?>

        <div class="form-group">
            <div class="col-lg-offset-3 col-lg-9">
                <?= Html::submitButton(Yii::t('app', 'Update'), ['class' => 'btn btn-block btn-primary']) ?>
            </div>
        </div>

        <?php ActiveForm::end(); ?>
    </div>
</div>

<?php $this->endContent() ?>
