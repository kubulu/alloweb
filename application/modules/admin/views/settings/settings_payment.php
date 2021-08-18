<?php

use app\helpers\Url;
use app\helpers\Html;
use yii\widgets\ActiveForm;
use app\settings\SettingsForm;
use wbraganca\dynamicform\DynamicFormWidget;

/** @var $settingsManager \app\settings\SettingsManager */
/** @var $settingsModel \app\settings\SettingsModel */
/** @var $this \yii\web\View */
/** @var $title string */
/** @var $currencies \app\models\Currency[] */

$title = Yii::t('app', 'Settings');
$this->title = $title;
$this->params['breadcrumbs'][] = ['label' => $title, 'url' => Url::current()];

$this->beginContent('@app/modules/admin/views/settings/_layout.php');
?>

<?= SettingsForm::widget([
    'manager' => $settingsManager,
    'model' => $settingsModel,
    'formView' => '@app/modules/admin/views/partials/settings_form',
    'title' => $title,
]) ?>

<div class="box box-primary">
    <?php $form = ActiveForm::begin(['id' => 'dynamic-form', 'action' => ['settings-currencies']]); ?>
    <div class="box-header with-border">
        <h3 class="box-title"><?= Yii::t('app', 'Currencies') ?></h3>
    </div>
    <div class="box-body">
        <div class="alert alert-success">
            <?= Yii::t('app', 'Check this this page {link} for {code} reference', [
                'link' => Html::a('https://en.wikipedia.org/wiki/ISO_4217', 'https://en.wikipedia.org/wiki/ISO_4217', [
                    'target' => '_blank',
                ]),
                'code' => '<code>code</code>',
            ]) ?>
        </div>
        <?php DynamicFormWidget::begin([
            'widgetContainer' => 'currencies_wrapper',
            'widgetBody' => '.container-items',
            'widgetItem' => '.item',
            'limit' => 10,
            'min' => 1,
            'insertButton' => '.add-item',
            'deleteButton' => '.remove-item',
            'model' => count($currencies) ? $currencies[0] : new \app\models\Currency(),
            'formId' => 'dynamic-form',
            'formFields' => [
                'code',
                'title',
                'format',
            ],
        ]); ?>
        <div class="container-items">
            <?php foreach ($currencies as $i => $model): ?>
                <div class="item">
                    <?php if (!$model->isNewRecord): ?>
                        <?= Html::activeHiddenInput($model, "[{$i}]id") ?>
                    <?php endif; ?>
                    <div class="row">
                        <div class="col-sm-2">
                            <?= $form->field($model, "[{$i}]code")->textInput() ?>
                        </div>
                        <div class="col-sm-5">
                            <?= $form->field($model, "[{$i}]title")->textInput() ?>
                        </div>
                        <div class="col-sm-3">
                            <?= $form->field($model, "[{$i}]format")->textInput() ?>
                        </div>
                        <div class="col-sm-2">
                            <div class="form-group text-center">
                                <div class="control-label">&nbsp;</div>
                                <button type="button" class="add-item btn btn-success btn-sm"><i class="glyphicon glyphicon-plus"></i></button>
                                <button type="button" class="remove-item btn btn-danger btn-sm"><i class="glyphicon glyphicon-minus"></i></button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <?php DynamicFormWidget::end(); ?>
    </div>
    <div class="box-footer">
        <?= Html::submitButton( Yii::t('app', 'Save'), ['class' => 'btn btn-primary']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>

<?php $this->endContent() ?>
