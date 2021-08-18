<?php

use app\models\Photo;
use app\helpers\Url;
use app\helpers\Html;

/** @var $model Photo */
/** @var $profile \app\models\Profile */

$profile = Yii::$app->user->identity->profile;
$previewUrl = Yii::$app->glide->createSignedUrl([
    'photo/thumbnail', 'id' => $model->id,
    'w' => 200, 'h' => 200, 'sharp' => 1, 'fit' => 'crop-center',
], true);
?>
<div class="photo-item col-sm-4 col-xl-3">
    <div class="card">
        <img class="card-img-top" src="<?= $previewUrl ?>">
        <div class="card-body d-flex flex-column pt-2 pl-2 pr-2 pb-2">
            <div class="d-flex align-items-center mt-auto">
                <?php if ($model->is_verified): ?>
                    <div>
                        <span class="text-muted"><?= Yii::t('youdate', 'Verified') ?></span>
                    </div>
                <?php endif; ?>
                <div class="ml-auto">
                    <?= Html::a('<i class="fe fe-check"></i>', ['/photo/set-main', 'id' => $model->id], [
                        'class' => 'btn btn-ajax btn-sm btn-' . ($profile->photo_id == $model->id ? 'primary' : 'secondary'),
                        'data-pjax-container' => '#pjax-settings-photos',
                        'data-type' => 'post',
                    ]) ?>
                    <?= Html::a('<i class="fe fe-trash"></i>', ['/photo/delete', 'id' => $model->id], [
                        'class' => 'btn btn-ajax btn-sm btn-danger',
                        'data-pjax-container' => '#pjax-settings-photos',
                        'data-confirm-title' => Yii::t('youdate', 'Delete this photo?'),
                        'data-title' => Yii::t('youdate', 'Delete photo'),
                        'data-type' => 'post',
                    ]) ?>
                </div>
            </div>
        </div>
    </div>
</div>
