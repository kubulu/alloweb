<?php

use app\helpers\Html;
use app\helpers\Url;
use app\models\Profile;

/** @var $this \app\base\View */
/** @var $spotlightUsers \app\models\Spotlight[] */
/** @var $user \app\models\User */
/** @var $profile \app\models\Profile */
/** @var $spotlightForm \app\forms\SpotlightForm */
/** @var $price int */
/** @var $userPhotos array */
?>
<?php \yii\widgets\Pjax::begin(['id' => 'pjax-spotlight-horizontal', 'linkSelector' => false, 'formSelector' => false]) ?>
<div class="spotlight-items-horizontal d-sm-flex flex-row">
    <a href="#" class="spotlight-item spotlight-item-submit" data-toggle="modal" data-target="#spotlight-submit">
        <div class="avatar d-flex flex-column justify-content-center align-items-center" style="background-image: url('<?= $profile->getAvatarUrl() ?>')">
            <div class="icon"><i class="fa fa-plus"></i></div>
            <div class="label d-none d-md-block"><?= Yii::t('youdate', 'Add me') ?></div>
        </div>
        <div class="name text-center d-none d-md-block">
            <?= Html::encode($user->profile->getDisplayName()) ?>
        </div>
    </a>
    <?php foreach ($spotlightUsers as $spotlight): ?>
        <a href="<?= Url::to(['/profile/view', 'username' => $spotlight->user->username]) ?>" class="spotlight-item">
            <div class="avatar" style="background-image: url('<?= $spotlight->photo->getThumbnail(Profile::AVATAR_NORMAL, Profile::AVATAR_NORMAL) ?>')"></div>
            <div class="name text-center d-none d-md-block" rel="tooltip" title="<?= Html::encode($spotlight->message) ?>">
                <?= Html::encode($spotlight->user->profile->getDisplayName()) ?>
            </div>
        </a>
    <?php endforeach; ?>
</div>
<?php \yii\widgets\Pjax::end() ?>
<?= $this->render('submit', ['spotlightForm' => $spotlightForm, 'price' => $price, 'userPhotos' => $userPhotos]) ?>
