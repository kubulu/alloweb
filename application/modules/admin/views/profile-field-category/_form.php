<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\ProfileFieldCategory */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="profile-field-category-form">
    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'alias')
        ->textInput(['maxlength' => true])
        ->hint(Yii::t('app', 'Alphanumeric symbols only'))?>

    <?= $form->field($model, 'language_category')
        ->textInput(['maxlength' => true])
        ->hint(Yii::t('app', 'Default is {0}', '<code>app</code>'))?>

    <?= $form->field($model, 'sort_order')
        ->textInput()
        ->hint(Yii::t('app', 'Default is {0}', 100))?>

    <?= $form->field($model, 'is_visible')->checkbox() ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>
