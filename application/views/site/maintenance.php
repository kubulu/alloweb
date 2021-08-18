<?php

use app\helpers\Html;

/** @var $this \yii\web\View */

$this->title = Yii::t('app', 'Maintenance');
?>
<h1><?= Yii::t('app', 'We\'ll be back soon!') ?></h1>
<div>
    <p>
        <?= Yii::t('app',
    'Sorry for the inconvenience but we\'re performing some maintenance at the moment.') ?>
    </p>
    <p class="text-muted"><?= Html::encode(Yii::$app->name) ?></p>
</div>
