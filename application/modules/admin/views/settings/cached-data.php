<?php

use app\helpers\Html;

/** @var $settingsManager \app\settings\SettingsManager */
/** @var $settingsModel \app\settings\SettingsModel */
/** @var $this \yii\web\View */
/** @var $title string */
?>

<?php $this->beginContent('@app/modules/admin/views/settings/_layout.php') ?>

<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title"><?= Yii::t('app', 'Cached data') ?></h3>
    </div>
    <div class="box-body">
        <p><?= Yii::t('app', 'This action will') ?>:</p>
        <ul>
            <li><?= Yii::t('app', 'Delete cached data') ?></li>
            <li><?= Yii::t('app', 'Delete cached assets (css, js, etc)') ?></li>
        </ul>
    </div>
    <div class="box-footer">
        <?= Html::a('<i class="fa fa-trash"></i> ' . Yii::t('app', 'Flush data'), ['cached-data'], [
            'class' => 'btn btn-primary',
            'data-method' => 'post'
        ]) ?>
    </div>
</div>

<?php $this->endContent() ?>
