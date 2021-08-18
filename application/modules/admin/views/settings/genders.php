<?php

use app\helpers\Url;
use app\helpers\Html;
use yii\widgets\ActiveForm;
use wbraganca\dynamicform\DynamicFormWidget;

/** @var $settingsManager \app\settings\SettingsManager */
/** @var $settingsModel \app\settings\SettingsModel */
/** @var $this \yii\web\View */
/** @var $title string */
/** @var $genders \app\models\Sex[] */

$title = Yii::t('app', 'Gender settings');
$this->title = $title;
$this->params['breadcrumbs'][] = ['label' => $title, 'url' => Url::current()];

$this->beginContent('@app/modules/admin/views/settings/_layout.php');
?>

<div class="box box-primary">
    <?php $form = ActiveForm::begin(['id' => 'dynamic-form', 'action' => ['genders']]); ?>
    <div class="box-header with-border">
        <h3 class="box-title"><?= Yii::t('app', 'Gender settings') ?></h3>
    </div>
    <div class="box-body">
        <?php DynamicFormWidget::begin([
            'widgetContainer' => 'genders_wrapper',
            'widgetBody' => '.container-items',
            'widgetItem' => '.item',
            'limit' => 30,
            'min' => 1,
            'insertButton' => '.add-item',
            'deleteButton' => '.remove-item',
            'model' => count($genders) ? $genders[0] : new \app\models\Sex(),
            'formId' => 'dynamic-form',
            'formFields' => [
                'alias',
                'title',
                'title_plural',
                'icon',
            ],
        ]); ?>
        <div class="container-items">
            <div class="alert alert-info">
                <?= Yii::t('app', 'Icons: Font Awesome are used by default - {0}',
                    Html::a('https://fontawesome.com/v4.7.0/icons/',  'https://fontawesome.com/v4.7.0/icons/')) ?>
            </div>
            <?php foreach ($genders as $i => $model): ?>
                <div class="item">
                    <?php if (!$model->isNewRecord): ?>
                        <?= Html::activeHiddenInput($model, "[{$i}]id") ?>
                    <?php endif; ?>
                    <div class="row">
                        <div class="col-sm-2">
                            <?= $form->field($model, "[{$i}]alias")->textInput() ?>
                        </div>
                        <div class="col-sm-3">
                            <?= $form->field($model, "[{$i}]title")->textInput() ?>
                        </div>
                        <div class="col-sm-3">
                            <?= $form->field($model, "[{$i}]title_plural")->textInput() ?>
                        </div>
                        <div class="col-sm-2">
                            <?= $form->field($model, "[{$i}]icon")->textInput() ?>
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
