<?php

/** @var $popularity string */
/** @var $value float|integer */
/** @var $color string */
/** @var $title string */

?>
<div class="card">
    <div class="card-body">
        <h5><?= Yii::t('youdate', 'Popularity') ?> &mdash; <span class="text-muted"><?= $title ?></span></h5>
        <div class="user-popularity">
            <div class="progress progress-xs">
                <div class="progress-bar bg-<?= $color ?>" role="progressbar"
                     style="width: <?= $value ?>%" aria-valuenow="<?= $value ?>" aria-valuemin="0" aria-valuemax="100"></div>
            </div>
        </div>
    </div>
</div>
