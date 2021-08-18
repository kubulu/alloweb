<?php

use yii\helpers\Html;
use yii\widgets\ListView;
use app\models\Photo;

/** @var $this \yii\web\View */
/** @var $type integer */
/** @var $dataProvider \yii\data\ActiveDataProvider */
/** @var $searchModel \app\modules\admin\models\Photo */
$userUrl = null;
if ($user) {
    $this->title = Yii::t('app', 'Photos by {username}', ['username' => $user->username]);
    $this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Manage photos'), 'url' => ['index']];
    $this->params['breadcrumbs'][] = $this->title;
    $urlUnverified = ['index', 'unverified' => 1, 'userId' => $user->id];
    $urlAll = ['index', 'unverified' => 0, 'userId' => $user->id];
    $userUrl = \app\helpers\Url::to(['user/info', 'id' => $user->id]);
} else {
    $this->title = Yii::t('app', 'Manage photos');
    $this->params['breadcrumbs'][] = $this->title;
    $urlUnverified = ['index', 'unverified' => 1];
    $urlAll = ['index', 'unverified' => 0];
}
?>
<div class="filters">
    <?= Html::a(Yii::t('app', 'Unverified photos'), $urlUnverified,
        ['class' => 'btn btn-sm ' . (!$type == Photo::NOT_VERIFIED ? 'btn-primary' : 'btn-default')]) ?>
    <?= Html::a(Yii::t('app', 'All photos'), $urlAll,
        ['class' => 'btn btn-sm ' . (!$type != Photo::NOT_VERIFIED ? 'btn-primary' : 'btn-default')]) ?>
</div>
<div class="nav-tabs-custom">
    <?php if ($user !== null): ?>
    <div class="user-header">
        <div class="row">
            <div class="col-xs-12 col-sm-8">
                <div class="user-image">
                    <a href="<?= $userUrl ?>"><img src="<?= $user->profile->getAvatarUrl(128, 128) ?>"></a>
                </div>
                <div class="user-info">
                    <div class="user-name"><?= Html::encode($user->profile->getDisplayName()) ?></div>
                    <div class="user-username"><?= Html::encode($user->username) ?></div>
                </div>
            </div>
            <div class="col-xs-12 col-sm-4">
                <?= Html::a('&larr; ' . Yii::t('app', 'Back to user'), $userUrl, [
                    'class' => 'btn btn-default pull-right',
                ]) ?>
            </div>
        </div>
    </div>
    <?php endif; ?>
    <div class="tab-content">
        <?php \yii\widgets\Pjax::begin(['id' => 'pjax-list-view']) ?>
            <?= ListView::widget([
                'dataProvider' => $dataProvider,
                'layout' => "{summary}\n<div class=\"clearfix\">{items}</div>\n{pager}",
                'options' => ['class' => 'list-view photos-list-view clearfix'],
                'itemView' => '_item',
                'itemOptions' => ['tag' => false],
            ]); ?>
        <?php \yii\widgets\Pjax::end() ?>
    </div>
</div>
