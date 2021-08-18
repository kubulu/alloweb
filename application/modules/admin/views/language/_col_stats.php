<?php

/** @var $value int */

?>
<div class="progress progress-stats">
    <div class="progress-bar progress-bar-green" role="progressbar"
         aria-valuenow="<?= $value ?>" aria-valuemin="0" aria-valuemax="100" style="width: <?= $value ?>%">
        <span><?= $value ?>%</span>
    </div>
</div>
