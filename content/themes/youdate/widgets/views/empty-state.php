<?php

use app\helpers\Html;

/** @var $icon string */
/** @var $title string */
/** @var $subTitle string */
/** @var $action string */
/** @var $options array */

?>
<?= Html::beginTag('div', $options) ?>
<div class="empty-icon">
    <i class="<?= $icon ?>"></i>
</div>
<h5 class="empty-title"><?= $title ?></h5>
<p class="empty-subtitle"><?= $subTitle ?></p>
<?php if (isset($action)): ?>
    <div class="empty-action">
        <?= $action ?>
    </div>
<?php endif; ?>
<?= Html::endTag('div') ?>
