<?php

/** @var $model \app\models\Notification */

/** @var \app\notifications\BaseNotification|null $baseModel */
$baseModel = $model->getBaseModel();
?>
<?php if ($baseModel !== null): ?>
<div class="notification-item d-flex mt-2 <?= $model->is_viewed ? 'viewed' : 'new' ?>">
    <div class="avatar mr-3 align-self-center"
          style="background-image: url('<?= $baseModel->sender->profile->getAvatarUrl(64, 64) ?>')">
    </div>
    <div>
        <?= $baseModel->html() ?>
        <div class="small text-muted">
            <?= date('Y-m-d H:i', $model->created_at) ?>
        </div>
    </div>
</div>
<?php endif; ?>
