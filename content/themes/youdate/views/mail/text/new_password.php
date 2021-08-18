<?php

/** @var \app\models\User $user */
/** @var \app\models\Token $token */
/** @var \app\base\View $this */

?>
<?= Yii::t('youdate', 'Hello') ?>,

<?= Yii::t('youdate', 'Your account on {0} has a new password', $this->frontendSetting('siteName')) ?>.
<?= Yii::t('youdate', 'We have generated a password for you') ?>:
<?= $user->password ?>

<?= Yii::t('youdate', 'If you did not make this request you can ignore this email') ?>.
