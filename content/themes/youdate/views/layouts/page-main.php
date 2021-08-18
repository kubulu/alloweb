<?php

/* @var $content string */

?>
<?php $this->beginContent('@theme/views/layouts/base.php'); ?>
<div class="page">
    <div class="page-main">
        <?= $this->render('//partials/header') ?>
        <?php if (!Yii::$app->user->isGuest): ?>
            <?= $this->render('//partials/header-navigation') ?>
        <?php endif; ?>
        <div class="content my-3 my-md-5">
            <div class="container">
                <?php echo $content ?>
            </div>
        </div>
    </div>
    <?= $this->render('//partials/footer') ?>
</div>
<?php $this->endContent(); ?>
