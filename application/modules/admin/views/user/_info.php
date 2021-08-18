<?php

/** @var $this \yii\web\View */use app\helpers\Html;
/** @var $user \app\models\User */

$this->title = 'User info';
?>

<?php $this->beginContent('@app/modules/admin/views/user/update.php', ['user' => $user]) ?>

<div class="box box-default">
    <div class="box-header with-border">
        <h2 class="box-title"><?= Yii::t('app', 'Account info') ?></h2>
    </div>
    <div class="box-body no-padding">
        <table class="table">
            <tr>
                <td><strong><?= Yii::t('app', 'User ID') ?>:</strong></td>
                <td><?= $user->id ?></td>
            </tr>
            <tr>
                <td><strong><?= Yii::t('app', 'Registration time') ?>:</strong></td>
                <td><?= Yii::t('app', '{0, date, MMMM dd, YYYY HH:mm}', [$user->created_at]) ?></td>
            </tr>
            <?php if ($user->registration_ip !== null): ?>
                <tr>
                    <td><strong><?= Yii::t('app', 'Registration IP') ?>:</strong></td>
                    <td><?= $user->registration_ip ?></td>
                </tr>
            <?php endif ?>
            <tr>
                <td><strong><?= Yii::t('app', 'Confirmation status') ?>:</strong></td>
                <?php if ($user->isConfirmed): ?>
                    <td class="text-success">
                        <?= Yii::t('app', 'Confirmed at {0, date, MMMM dd, YYYY HH:mm}', [$user->confirmed_at]) ?>
                    </td>
                <?php else: ?>
                    <td class="text-danger"><?= Yii::t('app', 'Unconfirmed') ?></td>
                <?php endif ?>
            </tr>
            <tr>
                <td><strong><?= Yii::t('app', 'Block status') ?>:</strong></td>
                <?php if ($user->isBlocked): ?>
                    <td class="text-danger">
                        <?= Yii::t('app', 'Blocked at {0, date, MMMM dd, YYYY HH:mm}', [$user->blocked_at]) ?>
                    </td>
                <?php else: ?>
                    <td class="text-success"><?= Yii::t('app', 'Not blocked') ?></td>
                <?php endif ?>
            </tr>
        </table>

    </div>
</div>

<div class="box box-default">
    <div class="box-header with-border">
        <h2 class="box-title"><?= Yii::t('app', 'Profile info') ?></h2>
    </div>
    <div class="box-body no-padding">
        <table class="table">
            <tr>
                <td><strong><?= Yii::t('app', 'Name') ?>:</strong></td>
                <td><?= Html::encode($user->profile->name) ?></td>
            </tr>
            <tr>
                <td><strong><?= Yii::t('app', 'Description') ?>:</strong></td>
                <td><?= Html::encode($user->profile->description) ?></td>
            </tr>
            <tr>
                <td><strong><?= Yii::t('app', 'Sex') ?>:</strong></td>
                <td><?= Html::encode($user->profile->getSexTitle()) ?></td>
            </tr>
            <tr>
                <td><strong><?= Yii::t('app', 'Status') ?>:</strong></td>
                <td><?= Html::encode($user->profile->getStatusTitle()) ?></td>
            </tr>
            <tr>
                <td><strong><?= Yii::t('app', 'Location') ?>:</strong></td>
                <td><?= Html::encode($user->profile->getDisplayLocation()) ?></td>
            </tr>
            <tr>
                <td><strong><?= Yii::t('app', 'Birthdate') ?>:</strong></td>
                <td><?= Html::encode($user->profile->dob) ?></td>
            </tr>
        </table>
    </div>
</div>

<?php $this->endContent() ?>
