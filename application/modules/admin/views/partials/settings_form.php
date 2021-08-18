<?php

use app\settings\SettingsModel;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Alert;
use yii\bootstrap\Button;
use trntv\aceeditor\AceEditor;

/** @var $elements array */
/** @var $title string */
/** @var $model SettingsModel */

$message = \Yii::$app->session->getFlash('settings');
?>
<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title"><?= Html::encode($title) ?></h3>
    </div>
    <?php $form = ActiveForm::begin() ?>
    <div class="box-body">
        <?php if ($message) {
            echo Alert::widget([
                'options' => [
                    'class' => 'alert-success',
                ],
                'body' => $message
            ]);
        }
        foreach ($elements as $alias => $element) {
            if (is_callable($element['type'])) {
                echo $element['type']($model, $alias);
            } else {
                switch ($element['type']) {
                    case 'dropdown':
                        echo $form->field($model, $alias)
                            ->dropDownList(is_callable($element['options']) ? $element['options']() : $element['options'], [
                                'prompt' => Yii::t('app', '-- Select --'),
                            ])
                            ->hint($element['help']);
                        break;
                    case 'checkboxList':
                        echo $form->field($model, $alias)->checkboxList($element['options'])->hint($element['help']);
                        break;
                    case 'checkbox':
                        echo $form->field($model, $alias)->checkbox(isset($element['options']) ?: [])->hint($element['help']);
                        break;
                    case 'code':
                        echo $form->field($model, $alias)->widget(AceEditor::class, ArrayHelper::merge(isset($element['options']) ? $element['options'] : [], [
                            'mode' => 'php',
                            'containerOptions' => [
                                'style' => 'width: 100%; min-height: 200px'
                            ]
                        ]))->hint($element['help']);;
                        break;
                    default:
                    case 'text':
                        echo $form->field($model, $alias)->hint($element['help']);;
                }
            }
        } ?>
    </div>
    <div class="box-footer">
        <?= Button::widget([
            'label' => Yii::t('app', 'Save'),
            'options' => ['class' => 'btn-primary']
        ]) ?>
    </div>
    <?php $form->end() ?>
</div>
