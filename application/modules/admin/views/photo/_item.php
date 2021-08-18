<?php

use app\helpers\Html;
use app\helpers\Url;
use rmrevin\yii\fontawesome\FA;

/** @var $model \app\modules\admin\models\Photo */
?>
<div class="list-item photo-item col-xs-12 col-md-4 col-lg-3" data-photo-id="<?= $model->id ?>">
    <a href="<?= $model->getUrl() ?>" data-fancybox="gallery">
        <?= Html::img(Yii::$app->glide->createSignedUrl([
            'photo/thumbnail', 'id' => $model->id, 'w' => 600, 'h' => 300, 'fit' => 'crop'
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
            <?= Html::button(FA::i('trash'), [
                'rel' => 'tooltip',
                'title' => Yii::t('app', 'Delete'),
                'class' => 'btn btn-danger btn-sm btn-action delete',
                'data-url' => Url::to(['delete', 'id' => $model->id]),
            ]) ?>
            <a class="photo-user" data-pjax="0" href="<?= Url::to(['user/info', 'id' => $model->user_id]) ?>">
                <img src="<?= $model->userProfile->getAvatarUrl(60, 60) ?>" alt="">
                <span><?= Html::encode($model->user->username )?></span>
            </a>
        </div>
    </div>
</div>
