<?php

use yii\helpers\Html;
use app\helpers\Url;

?>
<div class="header py-4">
    <div class="container">
        <div class="d-flex">
            <a class="header-brand" href="<?= Url::to(['/']) ?>">
                <i class="fa fa-heart pr-2" style="color: #f66d9b"></i> Custom Logo
            </a>
            <div class="d-flex align-items-center order-lg-2 ml-auto">
                <?php if (!Yii::$app->user->isGuest): ?>
                    <?= \youdate\widgets\Notifications::widget() ?>
                    <?= \youdate\widgets\UserMenu::widget() ?>
                    <a href="#" class="header-toggler d-lg-none ml-3 ml-lg-0" data-toggle="collapse" data-target="#header-navigation">
                        <span class="header-toggler-icon"></span>
                    </a>
                <?php else: ?>
                    <span class="pr-4"><?= Yii::t('custom', 'Have an account?') ?></span>
                    <?= Html::a(Yii::t('custom', 'Sign in'), ['/security/login'], ['class' => 'btn btn-primary']) ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
