<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\Language;

/* @var $this yii\web\View */
/* @var $model app\models\Language */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="language-form">
    <?php $form = ActiveForm::begin([
        'enableAjaxValidation' => true,
    ]); ?>

    <div class="row">
        <div class="col-xs-12 col-sm-4">
            <?= $form->field($model, 'language_id')
                ->textInput(['maxlength' => 5])
                ->hint(Yii::t('app', 'Example') . ': en-US') ?>
        </div>
        <div class="col-xs-12 col-sm-4">
            <?= $form->field($model, 'language')
                ->textInput(['maxlength' => 3])
                ->hint(Yii::t('app', 'Example') . ': en') ?>
        </div>
        <div class="col-xs-12 col-sm-4">
            <?= $form->field($model, 'country')
                ->textInput(['maxlength' => 3])
                ->hint(Yii::t('app', 'Example') . ': us') ?>
        </div>
    </div>

    <div class="row">
        <div class="col-xs-12 col-sm-4">
            <?= $form->field($model, 'name')->textInput(['maxlength' => 32]) ?>
        </div>
        <div class="col-xs-12 col-sm-4">
            <?= $form->field($model, 'name_ascii')->textInput(['maxlength' => 32]) ?>
        </div>
        <div class="col-xs-12 col-sm-4">
            <?= $form->field($model, 'status')->dropDownList(Language::getStatusNames()) ?>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'),
            ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>
