<?php

/** @var $this \yii\web\View */
/** @var $content string */

?>
<?php $this->beginContent('@app/views/layouts/main.php') ?>
<div class="py-5">
    <h2>YouDate Installer</h2>
    <div class="text-muted">Version <span class="badge badge-primary"><?= version() ?></span></div>
    <hr>
</div>
<?= $content ?>
<?php $this->endContent() ?>
