<?php

use yii\helpers\Html;

/** @var $icon string */
/** @var $title string */
/** @var $message string */
/** @var $action array */

$action = isset($action) ? $action : null;
?>
<div class="page-empty">
    <div class="empty-icon">
        <i class="fa fa-<?= $icon ?>"></i>
    </div>
    <h3 class="empty-title"><?= Html::encode($title) ?></h3>
    <div class="empty-message text-muted"><?= Html::encode($message) ?></div>
    <?php if ($action): ?>
        <div class="empty-action">
            <?= Html::a($action['label'], $action['url'], ['class' => 'btn btn-primary btn-md']) ?>
        </div>
    <?php endif ?>
</div>
