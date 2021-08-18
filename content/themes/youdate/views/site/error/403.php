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
    <div class="display-1 text-muted mb-5"><i class="si si-exclamation"></i> 403</div>
    <h1 class="h2 mb-3"><?= Yii::t('youdate', 'Access denied') ?></h1>
    <p class="h4 text-muted font-weight-normal mb-7">
        <?= Yii::t('youdate', 'You have no access to the requested page') ?></p>
    <a class="btn btn-primary" href="<?= Url::to(['/']) ?>">
        <i class="fe fe-arrow-left mr-2"></i><?= Yii::t('youdate', 'Go back') ?>
    </a>
</div>
