<?php

use app\managers\LikeManager;
use app\helpers\Html;

/** @var $type string */

$message = '';
switch ($type) {
    case LikeManager::TYPE_FROM_CURRENT_USER:
        $message = Yii::t('youdate', 'You did\'t like anyone yet');
        break;
    case LikeManager::TYPE_TO_CURRENT_USER:
        $message = Yii::t('youdate', 'No one liked you yet');
        break;
    case LikeManager::TYPE_MUTUAL:
        $message = Yii::t('youdate', 'You don\'t have any mutual likes yet');
        break;
}
?>
<div class="container">
    <div class="card">
        <div class="card-body">
            <?= \youdate\widgets\EmptyState::widget([
                'icon' => 'fe fe-users',
                'subTitle' => Yii::t('youdate', $message),
                'action' => Html::a(Yii::t('youdate', 'Browse people'), ['/directory/index'], [
                    'class' => 'btn btn-primary',
                ]),
            ]) ?>
        </div>
    </div>
</div>
