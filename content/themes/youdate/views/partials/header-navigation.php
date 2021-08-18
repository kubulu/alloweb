<?php

use yii\helpers\ArrayHelper;
use youdate\widgets\HeaderNavigation;
use app\managers\LikeManager;

$countersMessagesNew = ArrayHelper::getValue($this->params, 'counters.messages.new');

?>
<div class="header collapse d-lg-block p-0" id="header-navigation">
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
                            'label' => Yii::t('youdate', 'Dashboard'),
                            'url' => ['dashboard/index'],
                            'icon' => 'home'
                        ],
                        [
                            'label' => Yii::t('youdate', 'Browse'),
                            'url' => ['directory/index'],
                            'icon' => 'user'
                        ],
                        [
                            'label' => Yii::t('youdate', 'Connections'),
                            'active' => $this->context instanceof \app\controllers\ConnectionsController,
                            'url' => ['connections/encounters'],
                            'icon' => 'heart'
                        ],
                        [
                            'label' => Yii::t('youdate', 'Messages'),
                            'url' => ['messages/index'],
                            'icon' => 'mail',
                            'count' => $countersMessagesNew,
                        ],
                    ],
                ]) ?>
            </div>
        </div>
    </div>
</div>
