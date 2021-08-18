<?php

use app\helpers\Html;
use app\helpers\Url;

/** @var bool $showBalance */
/** @var \app\base\View $this */
/** @var \app\models\User $user */
$user = Yii::$app->user->identity;
/** @var \app\models\Profile $profile */
$profile = $user->profile;
?>
<?php if ($showBalance): ?>
    <div class="nav-item d-none d-sm-block">
        <a href="<?= Url::to(['balance/services']) ?>"
           class="btn btn-outline-primary btn-sm"
           data-pjax="0"
           title="<?= Yii::t('youdate', 'Balance') ?>" rel="tooltip">
            <i class="fa fa-money mr-2"></i><span class="user-balance"><?= $this->params['user.balance'] ?></span>
        </a>
    </div>
<?php endif; ?>
<div class="dropdown">
    <a href="<?= Url::to(['/profile/index']) ?>" class="nav-link pr-0 leading-none" data-toggle="dropdown">
        <span class="avatar" style="background-image: url(<?= $profile->getAvatarUrl(64, 64) ?>)"></span>
        <span class="ml-2 d-none d-lg-block">
            <span class="text-default"><?= Html::encode($profile->getDisplayName()) ?></span>
            <small class="text-muted d-block mt-1"><?= Html::encode($user->username) ?></small>
        </span>
    </a>
    <div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow">
        <a class="dropdown-item" href="<?= Url::to(['/profile/index']) ?>">
            <i class="dropdown-icon fe fe-user"></i> <?= Yii::t('youdate', 'Profile') ?>
        </a>
        <a class="dropdown-item" href="<?= Url::to(['/settings/profile']) ?>">
            <i class="dropdown-icon fe fe-settings"></i> <?= Yii::t('youdate', 'Settings') ?>
        </a>
        <a class="dropdown-item d-block d-md-none" href="<?= Url::to(['/notifications/index']) ?>">
            <i class="dropdown-icon fe fe-bell"></i> <?= Yii::t('youdate', 'Notifications') ?>
        </a>
        <?php if ($showBalance): ?>
            <a class="dropdown-item d-block d-sm-none" href="<?= Url::to(['/balance/services']) ?>">
                <span class="float-right"><span class="badge badge-primary"><?= $this->params['user.balance'] ?></span></span>
                <i class="dropdown-icon fe fe-dollar-sign"></i> <?= Yii::t('youdate', 'Balance') ?>
            </a>
        <?php endif; ?>
        <?php if ($user->isAdmin): ?>
            <a class="dropdown-item" href="<?= Url::to(['/' . env('ADMIN_PREFIX')]) ?>">
                <i class="dropdown-icon fe fe-sliders"></i> <?= Yii::t('youdate', 'Administration') ?>
            </a>
        <?php endif; ?>
        <div class="dropdown-divider"></div>
        <a class="dropdown-item" href="<?= Url::to(['/site/page', 'view' => 'help']) ?>">
            <i class="dropdown-icon fe fe-help-circle"></i> <?= Yii::t('youdate', 'Need help?') ?>
        </a>
        <a class="dropdown-item" data-method="post" href="<?= Url::to(['/security/logout']) ?>">
            <i class="dropdown-icon fe fe-log-out"></i> <?= Yii::t('youdate', 'Sign out') ?>
        </a>
    </div>
</div>
