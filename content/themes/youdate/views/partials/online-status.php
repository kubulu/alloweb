<?php

/** @var $model \app\models\User */

?>
<?php if ($model->isOnline): ?>
    <i class="online-status bg-green" rel="tooltip"
       title="<?= Yii::t('youdate', 'Online') ?>">
    </i>
<?php else: ?>
    <i class="online-status bg-gray" rel="tooltip"
       title="<?= Yii::t('youdate', 'Last online: {date}', ['date' => $model->getLastTimeOnline()]) ?>">
    </i>
<?php endif; ?>
