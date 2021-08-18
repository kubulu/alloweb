<?php

/** @var \app\models\User $user */
/** @var \app\models\Token $token */
/** @var \app\base\View $this */

?>
<?= Yii::t('youdate', 'Hello') ?>,

<?= Yii::t('youdate', 'We have received a request to reset the password for your account on {0}', $this->frontendSetting('siteName')) ?>.
<?= Yii::t('youdate', 'Please click the link below to complete your password reset') ?>.

<?= $token->url ?>

<?= Yii::t('youdate', 'If you cannot click the link, please try pasting the text into your browser') ?>.

<?= Yii::t('youdate', 'If you did not make this request you can ignore this email') ?>.
