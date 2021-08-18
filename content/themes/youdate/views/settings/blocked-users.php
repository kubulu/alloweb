<?php

use app\helpers\Html;
use app\helpers\Url;

/** @var $this \app\base\View */
/** @var $blockedUsers \app\models\Block[] */

$this->title = Yii::t('youdate', 'Blocked users');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="card">
    <div class="card-header">
        <h3 class="card-title"><?= Html::encode($this->title) ?></h3>
    </div>
    <div class="card-body">
        <?= $this->render('/_alert') ?>
        <?php \yii\widgets\Pjax::begin(['id' => 'blocked-users-pjax']) ?>
            <?php foreach ($blockedUsers as $block): ?>
                <?php $profile = $block->blockedUser->profile ?>
                <div class="row d-flex align-items-center pb-3">
                    <div class="col">
                        <a href="<?= Url::to(['/profile/view', 'username' => $block->blockedUser->username]) ?>"
                            class="d-flex align-items-center text-dark" data-pjax="0">
                            <span class="avatar mr-5" style="background-image: url('<?= $profile->getAvatarUrl() ?>')"></span>
                            <strong><?= Html::encode($profile->getDisplayName()) ?></strong>
                        </a>
                    </div>
                    <div class="col">
                        <?= Html::a(Yii::t('youdate', 'Unblock'),
                            ['/block/delete'],
                            [
                                'class' => 'btn btn-danger btn-ajax float-right',
                                'data' => [
                                    'type' => 'post',
                                    'data' => 'blockedUserId=' . $block->blocked_user_id,
                                    'pjax-container' => '#blocked-users-pjax',
                                    'title' => Yii::t('youdate', 'Unblock user'),
                                    'confirm-title' => Yii::t('youdate', 'Do you really want to unblock user {username}?', [
                                        'username' => Html::encode($profile->getDisplayName()),
                                    ]),
                                    'cancel-button' => Yii::t('youdate', 'Cancel'),
                                    'confirm-button' => Yii::t('youdate', 'Unblock'),
                                ],
                            ]) ?>
                    </div>
                </div>
            <?php endforeach; ?>
            <?php if (!count($blockedUsers)): ?>
                <?= \youdate\widgets\EmptyState::widget([
                    'icon' => 'fe fe-users',
                    'subTitle' => Yii::t('youdate', 'You have no blocked users'),
                ]) ?>
            <?php endif; ?>
        <?php \yii\widgets\Pjax::end() ?>
    </div>
</div>
