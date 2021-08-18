<?php

use youdate\widgets\NotificationCategories;
use youdate\widgets\ListView;
use youdate\widgets\EmptyState;
use app\helpers\Html;

/** @var $dataProvider \yii\data\ActiveDataProvider */
/** @var $categories \app\notifications\BaseNotificationCategory[] */
/** @var $filters array|null */
/** @var $this \app\base\View */

$this->title = Yii::t('youdate', 'Notifications');
$this->context->layout = 'page-main';
$this->registerJsFile('@themeUrl/static/js/notifications.js', [
    'depends' => \youdate\assets\Asset::class,
]);
?>
<div class="row">
    <div class="col-sm-12 col-md-4 col-lg-3 order-0 order-md-1">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><?= Yii::t('youdate', 'Filters') ?></h3>
            </div>
            <div class="card-body">
                <?= NotificationCategories::widget(['categories' => $categories]) ?>
            </div>
        </div>
    </div>
    <div class="col-sm-12 col-md-8 col-lg-9 order-1 order-md-0">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><?= Yii::t('youdate', 'Notifications') ?></h3>
                <div class="card-options">
                    <?= Html::a(Yii::t('youdate', 'Mark all as viewed'), ['mark-as-viewed'], [
                        'class' => 'btn btn-primary btn-sm btn-ajax',
                        'data-pjax-container' => '#pjax-notifications',
                        'data-type' => 'post',
                        'onclick' => '$(".dropdown-notifications .nav-unread").addClass("hidden")',
                    ]) ?>
                </div>
            </div>
            <div class="card-body">
                <?php \yii\widgets\Pjax::begin(['id' => 'pjax-notifications', 'linkSelector' => false]) ?>
                    <?php if ($dataProvider->getTotalCount()): ?>
                        <?= ListView::widget([
                            'dataProvider' => $dataProvider,
                            'options' => ['class' => 'notifications-list'],
                            'itemView' => '_item',
                        ]) ?>
                    <?php else: ?>
                        <?= EmptyState::widget([
                            'icon' => 'fe fe-bell',
                            'title' => Yii::t('youdate', 'No notifications'),
                            'subTitle' => is_array($filters) ?
                                Yii::t('youdate', 'You don\'t have any notifications yet') :
                                Yii::t('youdate', 'Notifications not found'),
                        ]) ?>
                    <?php endif; ?>
                <?php \yii\widgets\Pjax::end() ?>
            </div>
        </div>
    </div>
</div>
