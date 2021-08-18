<?php

use app\helpers\Html;

/** @var \app\models\User $receiver */
/** @var \app\models\User $sender */
/** @var \app\notifications\BaseNotification $notification */
/** @var \app\base\View $this */

?>

<div style="height: 1px; width: 100%; background: #eee; margin-top: 15px"></div>

<p style="margin-top: 25px; margin-bottom: 25px;">
    <a href="<?= \app\helpers\Url::to(['/profile/view', 'username' => $sender->username], true) ?>">
        <img src="<?= $sender->profile->getAvatarUrl(200, 200) ?>"
             style="display: block; margin: auto; width: 100px; height: 100px; border-radius: 100px;"
             alt="<?= Html::encode($sender->profile->getDisplayName()) ?>">
    </a>
</p>

<p style="text-align: center; font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif; font-size: 22px; line-height: 1.6; font-weight: normal; margin: 0 0 10px; padding: 0;">
    <?= $notification->getMailSubject() ?>
</p>

<p style="text-align: center; font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif; font-size: 16px; color: #777; line-height: 1.6; font-weight: normal; margin: 0 0 10px; padding: 0;">
    <?= $notification->html() ?>
</p>
