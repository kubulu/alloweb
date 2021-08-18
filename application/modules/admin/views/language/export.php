<?php

use app\models\Language;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\Response;

/* @var $this yii\web\View */
/* @var $model \app\modules\admin\forms\LanguageExportForm */

$this->title = Yii::t('app', 'Export');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Manage languages'), 'url' => ['list']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="box box-solid">
    <div class="box-body">
        <div class="language-export col-sm-6">
            <?php $form = ActiveForm::begin(); ?>
            <?= $form->field($model, 'exportLanguages')
                ->listBox(ArrayHelper::map(Language::find()->all(), 'language_id', 'name_ascii'), [
                    'multiple' => true,
                    'size' => 20,
                ]) ?>
            <?= $form->field($model, 'format')->radioList([
                Response::FORMAT_JSON => Response::FORMAT_JSON,
                Response::FORMAT_XML => Response::FORMAT_XML,
            ]) ?>
            <div class="form-group">
                <?= Html::submitButton(Yii::t('app', 'Export'), ['class' => 'btn btn-primary']) ?>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
