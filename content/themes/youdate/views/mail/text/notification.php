<?php

/** @var \app\models\User $user */
/** @var \app\notifications\BaseNotification $notification */
/** @var \app\base\View $this */

?>
<?= $notification->getMailSubject() ?> ?>

<?= $notification->text() ?>
