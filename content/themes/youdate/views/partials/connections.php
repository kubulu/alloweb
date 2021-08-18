<?php

use app\managers\LikeManager;

echo \youdate\widgets\Sidebar::widget([
    'items' => [
        [
            'label' => Yii::t('youdate', 'People you like'),
            'url' => ['/like/index', 'type' => LikeManager::TYPE_FROM_CURRENT_USER],
            'icon' => 'user',
            'count' => $counters[LikeManager::TYPE_FROM_CURRENT_USER],
        ],
        [
            'label' => Yii::t('youdate', 'People who likes you'),
            'url' => ['/like/index', 'type' => LikeManager::TYPE_TO_CURRENT_USER],
            'icon' => 'user',
            'count' => $counters[LikeManager::TYPE_TO_CURRENT_USER],
        ],
        [
            'label' => Yii::t('youdate', 'Mutual likes'),
            'url' => ['/like/index', 'type' => LikeManager::TYPE_MUTUAL],
            'icon' => 'users',
            'count' => $counters[LikeManager::TYPE_MUTUAL],
        ],
    ],
]);

