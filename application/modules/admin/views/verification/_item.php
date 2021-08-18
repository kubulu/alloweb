<?php

use app\helpers\Html;
use app\helpers\Url;
use rmrevin\yii\fontawesome\FA;

/** @var $model \app\modules\admin\models\Verification */
?>
<div class="list-item photo-item col-xs-12 col-md-4 col-lg-3" data-photo-id="<?= $model->id ?>">
    <a href="<?= $model->getUrl() ?>" data-fancybox="gallery">
        <?= Html::img(Yii::$app->glide->createSignedUrl([
            env('ADMIN_PREFIX') . '/verification/thumbnail', 'id' => $model->id,
            'w' => 320, 'h' => 240, 'fit' => 'crop'
        ], true), ['class' => 'photo-preview']) ?>
    </a>
    <div class="photo-actions">
        <div class="clearfix">
            <?= Html::button(FA::i('check'), [
                'rel' => 'tooltip',
                'title' => Yii::t('app', 'Approve'),
                'class' => 'btn btn-success btn-sm btn-action approve',
                'data-url' => Url::to(['approve', 'id' => $model->id]),
            ]) ?>
            <?= Html::button(FA::i('times'), [
                'rel' => 'tooltip',
                'title' => Yii::t('app', 'Reject'),
                'class' => 'btn btn-danger btn-sm btn-action delete',
                'data-url' => Url::to(['reject', 'id' => $model->id]),
            ]) ?>
            <a class="photo-user" data-pjax="0" href="<?= Url::to(['user/info', 'id' => $model->user_id]) ?>">
                <img src="<?= $model->user->profile->getAvatarUrl(60, 60) ?>" alt="">
                <span><?= Html::encode($model->user->username )?></span>
            </a>
        </div>
    </div>
</div>
