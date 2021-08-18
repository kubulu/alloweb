<?php

use yii\helpers\Html;
use app\helpers\Url;
use yii\helpers\ArrayHelper;

/** @var $this \app\base\View */

?>
<div class="header py-4">
    <div class="container">
        <div class="d-flex">
            <a class="header-brand" href="<?= Url::to(['/']) ?>">
                <?= Html::img($this->themeSetting('logoUrl', '@themeUrl/static/images/logo@2x.png'), [
                    'class' => 'header-brand-img', 'alt' => $this->frontendSetting('siteName', 'YouDate')
                ]) ?>
            </a>
            <div class="d-flex align-items-center order-lg-2 ml-auto">
                <?php if (!Yii::$app->user->isGuest): ?>
                    <?= \youdate\widgets\Notifications::widget() ?>
                    <?= \youdate\widgets\UserMenu::widget([
                        'showBalance' => ArrayHelper::getValue($this->params, 'site.premiumFeatures.enabled'),
                    ]) ?>
                    <a href="#" class="header-toggler d-lg-none ml-3 ml-lg-0" data-toggle="collapse" data-target="#header-navigation">
                        <span class="header-toggler-icon"></span>
                    </a>
                <?php else: ?>
                    <span class="pr-4"><?= Yii::t('youdate', 'Have an account?') ?></span>
                    <?= Html::a(Yii::t('youdate', 'Sign in'), ['/security/login'], ['class' => 'btn btn-primary']) ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
