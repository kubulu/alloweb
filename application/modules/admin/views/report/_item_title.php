<?php

use yii\helpers\Html;

/** @var $model \app\modules\admin\models\Photo */
?>
<div class="photo-title">
    <?= isset($model->title) ? Html::encode($model->title) : Yii::t('app', 'No title') ?>
</div>
<div class="photo-alias">
    <?= isset($model->alias) ? '<code>' . Html::encode($model->alias) . '</code>' : Yii::t('app', 'No alias') ?>
</div>
