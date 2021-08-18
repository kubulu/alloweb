<?php

use app\helpers\Html;

?>
<?php foreach (Yii::$app->session->getAllFlashes() as $type => $message): ?>
    <?php if (in_array($type, ['success', 'danger', 'warning', 'info'])): ?>
        <div class="alert alert-<?= $type ?> alert-dismissible">
            <button type="button" class="close" data-dismiss="alert"></button>
            <?= Html::encode($message) ?>
        </div>
    <?php endif ?>
<?php endforeach ?>
