<?php

/* @var $this \app\base\View */
/* @var $name string */
/* @var $message string */
/* @var $exception Exception */

use app\helpers\Url;

$this->title = $name;
$this->context->layout = 'page-single';
?>
<div class="container text-center">
    <div class="display-1 text-muted mb-5"><i class="si si-exclamation"></i> 500</div>
    <h1 class="h2 mb-3"><?= Yii::t('youdate', 'Unexpected Error') ?></h1>
    <p class="h4 text-muted font-weight-normal mb-7">
        <?= Yii::t('youdate', 'An error occurred and your request could not be completed') ?></p>
    <a class="btn btn-primary" href="<?= Url::to(['/']) ?>">
        <?= Yii::t('youdate', 'Try again') ?>
    </a>
</div>
