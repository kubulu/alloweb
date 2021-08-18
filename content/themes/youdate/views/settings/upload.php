<?php

use app\helpers\Url;
use app\helpers\Html;
use yii\helpers\ArrayHelper;
use youdate\widgets\ActiveForm;
use youdate\widgets\Upload;

/** @var $model \app\models\Profile */
/** @var $form \yii\widgets\ActiveForm */
/** @var $this \app\base\View */
/** @var $uploadForm \app\forms\UploadForm */
/** @var $settings array */

$this->title = Yii::t('youdate', 'Upload Photos');
$this->params['breadcrumbs'][] = $this->title;
$this->registerJsFile('@themeUrl/static/js/settings.js', [
    'depends' => [
        \youdate\assets\Asset::class,
        \youdate\assets\UploadAsset::class,
    ],
]);
?>

<div class="card">
    <div class="card-header">
        <h3 class="card-title"><?= Html::encode($this->title) ?></h3>
        <div class="card-options">
            <a href="<?= Url::to(['/settings/photos']) ?>" class="btn btn-secondary btn-sm">
                <i class="fe fe fe-arrow-left"></i> <?= Yii::t('youdate', 'Back') ?>
            </a>
        </div>
    </div>
    <div class="card-body">
        <?= $this->render('/_alert', ['module' => Yii::$app->getModule('user')]) ?>

        <?php $form = ActiveForm::begin() ?>

        <?= $form->errorSummary($uploadForm) ?>

        <?= $form->field($uploadForm, 'photos')->widget(Upload::class, [
            'id' => 'photo-upload',
            'url' => ['/photo/upload-photo'],
            'multiple' => true,
            'sortable' => false,
            'maxFileSize' => ArrayHelper::getValue($settings, 'photoMaxFileSize', 20) * 1024 * 1024,
            'maxNumberOfFiles' => ArrayHelper::getValue($settings, 'photoMaxFiles', 10),
            'clientOptions' => [],
        ])->label(false); ?>

        <div class="form-group form-actions">
            <?= Html::submitButton(Yii::t('youdate', 'Upload'), ['class' => 'btn btn-primary']) ?>
        </div>

        <?php ActiveForm::end() ?>

    </div>
</div>
