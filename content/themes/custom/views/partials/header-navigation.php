<?php

use yii\helpers\ArrayHelper;
use youdate\widgets\HeaderNavigation;
use app\managers\LikeManager;

$countersMessagesNew = ArrayHelper::getValue($this->params, 'counters.messages.new');

?>
<div class="header collapse d-lg-flex p-0" id="header-navigation">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg order-lg-first">
                <?= HeaderNavigation::widget([
                    'options' => ['class' => 'nav nav-tabs border-0 flex-column flex-lg-row'],
                    'itemOptions' => [
                        'class' => 'nav-item',
                    ],
                    'items' => [
                        [
                            'label' => Yii::t('custom', 'Home'),
                            'url' => ['dashboard/index'],
                            'icon' => 'home'
                        ],
                        [
                            'label' => Yii::t('custom', 'Browse'),
                            'url' => ['directory/index'],
                            'icon' => 'user'
                        ],
                        [
                            'label' => Yii::t('custom', 'Encounters'),
                            'active' =>
                                $this->context instanceof \app\controllers\ConnectionsController &&
                                $this->context->action->id == 'encounters',
                            'url' => ['connections/encounters'],
                            'icon' => 'users'
                        ],
                        [
                            'label' => Yii::t('custom', 'Connections'),
                            'active' =>
                                $this->context instanceof \app\controllers\ConnectionsController &&
                                $this->context->action->id !== 'encounters',
                            'url' => ['connections/likes', 'type' => LikeManager::TYPE_FROM_CURRENT_USER],
                            'icon' => 'heart'
                        ],
                        [
                            'label' => Yii::t('custom', 'Messages'),
                            'url' => ['messages/index'],
                            'icon' => 'mail',
                            'count' => $countersMessagesNew,
                        ],
                        [
                            'label' => Yii::t('custom', 'Custom controller'),
                            'url' => ['custom/index'],
                            'icon' => 'mail',
                        ],
                    ],
                ]) ?>
            </div>
        </div>
    </div>
</div>
