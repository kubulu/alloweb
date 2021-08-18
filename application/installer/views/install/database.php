<?php

use yii\helpers\Html;

/** @var $this \yii\web\View */
/** @var $model \installer\forms\DatabaseForm */

$this->title = 'Database';
?>
<p class="text-muted">Database</p>

<?= Html::beginForm(['index'], 'post') ?>
<?= Html::errorSummary($model, ['class' => 'alert alert-danger']) ?>
<div class="row">
    <div class="col-sm-6">
        <div class="form-group">
            <label>Server (hostname):</label>
            <?= Html::activeTextInput($model, 'dbHost', ['class' => 'form-control']) ?>
        </div>
    </div>
    <div class="col-sm-6">
        <div class="form-group">
            <label>Database name:</label>
            <?= Html::activeTextInput($model, 'dbDatabase', ['class' => 'form-control']) ?>
        </div>
    </div>
    <div class="col-sm-6">
        <div class="form-group">
            <label>Username:</label>
            <?= Html::activeTextInput($model, 'dbUsername', ['class' => 'form-control']) ?>
        </div>
    </div>
    <div class="col-sm-6">
        <div class="form-group">
            <label>Password:</label>
            <?= Html::activePasswordInput($model, 'dbPassword', ['class' => 'form-control']) ?>
        </div>
    </div>
    <div class="col-sm-6">
        <div class="form-group">
            <label>Charset:</label>
            <?= Html::activeTextInput($model, 'dbCharset', ['class' => 'form-control']) ?>
        </div>
    </div>
</div>

<div class="actions mt-5 d-flex align-items-center justify-content-end">
    <?= Html::submitButton('Continue', ['class' => 'btn btn-primary']) ?>
</div>

<?= Html::endForm(); ?>
