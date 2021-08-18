<?php

use app\helpers\Html;
use app\helpers\Url;

/** @var $user \app\models\User */

?>
<div class="user-column">
    <a href="<?= Url::to(['user/info', 'id' => $user->id]) ?>" data-pjax="0">
        <?php if ($user->profile): ?>
            <img src="<?= $user->profile->getAvatarUrl(64, 64) ?>" alt="<?= $user->username ?>">
        <?php else: ?>
            <div class="no-photo">
                <i class="fa fa-user"></i>
            </div>
        <?php endif; ?>
        <div class="user-info">
            <div class="name"><?= Html::encode($user->profile ? $user->profile->getDisplayName() : 'User #' . $user->id) ?></div>
            <div class="username"><?= Html::encode($user->username) ?></div>
        </div>
    </a>
</div>
