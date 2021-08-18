<?php

/** @var \app\models\User $user */
/** @var \app\models\Token $token */
/** @var \app\base\View $this */

?>
<?= Yii::t('youdate', 'Hello') ?>,

<?= Yii::t('youdate', 'Thank you for signing up on {0}', $this->frontendSetting('siteName')) ?>.
<?= Yii::t('youdate', 'In order to complete your registration, please click the link below') ?>.

<?= $token->url ?>

<?= Yii::t('youdate', 'If you cannot click the link, please try pasting the text into your browser') ?>.

<?= Yii::t('youdate', 'If you did not make this request you can ignore this email') ?>.
