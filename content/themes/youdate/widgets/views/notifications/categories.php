<?php

use app\helpers\Url;

/** @var $categories \app\notifications\BaseNotificationCategory[] */

?>
<div class="notification-categories custom-controls-stacked" data-url="<?= Url::to(['/notifications/index']) ?>">
    <?php foreach ($categories as $category): ?>
        <label class="custom-control custom-checkbox">
            <input type="checkbox" class="custom-control-input" name="<?= $category->id ?>" value="1" checked="">
            <span class="custom-control-label">
                <?= Yii::t('youdate', $category->getTitle()) ?>
            </span>
        </label>
    <?php endforeach; ?>
</div>
