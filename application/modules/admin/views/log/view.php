<?php

use app\models\Log;

/** @var $model Log */
/** @var $this \yii\web\View */

$this->title = Yii::t('app', 'View log #{0}', $model->id);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Manage logs'), 'url' => ['index']];
?>
<div class="box box-solid">
    <div class="box-header with-border">
        <h3 class="box-title"><?= Yii::t('app', 'Logs') ?></h3>
    </div>
    <div class="box-body">
        <div>
            <strong><?= Yii::t('app', 'Category') ?>: </strong> <code><?= $model->category ?></code>
        </div>
        <div>
            <strong><?= Yii::t('app', 'Logged at') ?>: </strong> <code><?= Yii::$app->formatter->asDatetime($model->log_time) ?></code>
        </div>
    </div>
    <code style="display: block; padding: 10px;">
        <?= nl2br($model->message) ?>
    </code>
</div>
