<?php

use app\helpers\Html;
use app\helpers\Url;
use app\modules\admin\helpers\Language;
use app\models\Language as Lang;
use yii\widgets\Pjax;
use yii\grid\GridView;
use yii\widgets\ActiveForm;

/* @var $this \yii\web\View */
/* @var $language_id string */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $searchModel \app\modules\admin\models\search\LanguageSourceSearch */
/* @var $searchEmptyCommand string */

$this->title = Yii::t('app', 'Translation into {language_id}', ['language_id' => $language_id]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Manage languages'), 'url' => ['list']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="box box-solid">
    <div class="box-body">
        <?= Html::hiddenInput('language_id', $language_id, ['id' => 'language_id', 'data-url' => Url::to(['save'])]) ?>
        <div id="translates" class="<?= $language_id ?>">
            <?php
            Pjax::begin(['id' => 'translates']);
            $form = ActiveForm::begin([
                'method' => 'get',
                'id' => 'search-form',
                'enableAjaxValidation' => false,
                'enableClientValidation' => false,
            ]);
            echo $form->field($searchModel, 'source')->dropDownList(['' => Yii::t('app', 'Original')] + Lang::getLanguageNames(true))->label(Yii::t('app', 'Source language'));
            ActiveForm::end();
            echo GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],
                    [
                        'format' => 'raw',
                        'filter' => Language::getCategories(),
                        'attribute' => 'category',
                        'filterInputOptions' => ['class' => 'form-control', 'id' => 'category'],
                    ],
                    [
                        'format' => 'raw',
                        'attribute' => 'message',
                        'filterInputOptions' => ['class' => 'form-control', 'id' => 'message'],
                        'label' => Yii::t('app', 'Source'),
                        'content' => function ($data) {
                            return Html::textarea('LanguageSource[' . $data->id . ']', $data->source, ['class' => 'form-control source', 'readonly' => 'readonly']);
                        },
                    ],
                    [
                        'format' => 'raw',
                        'attribute' => 'translation',
                        'filterInputOptions' => [
                            'class' => 'form-control',
                            'id' => 'translation',
                            'placeholder' => $searchEmptyCommand ? Yii::t('app', 'Enter "{command}" to search for empty translations.', ['command' => $searchEmptyCommand]) : '',
                        ],
                        'label' => Yii::t('app', 'Translation'),
                        'content' => function ($data) {
                            return Html::textarea('LanguageTranslate[' . $data->id . ']', $data->translation, ['class' => 'form-control translation', 'data-id' => $data->id, 'tabindex' => $data->id]);
                        },
                    ],
                    [
                        'format' => 'raw',
                        'label' => Yii::t('app', 'Action'),
                        'content' => function ($data) {
                            return Html::button(Yii::t('app', 'Save'), ['type' => 'button', 'data-id' => $data->id, 'class' => 'btn btn-lg btn-success']);
                        },
                    ],
                ],
            ]);
            Pjax::end();
            ?>
        </div>
    </div>
</div>
