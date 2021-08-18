<?php

use app\managers\LikeManager;

/* @var $this \app\base\View */
/* @var $dataProvider \yii\data\ActiveDataProvider */
/* @var $type string */
/* @var $counters array */
/* @var $content string */

?>
<div class="page-content w-100 min-h-100">
    <div class="row h-100">
        <div class="col-lg-3 mb-4">
            <?= \youdate\widgets\Sidebar::widget([
                'header' => Yii::t('youdate', 'Connections'),
                'items' => [
                    [
                        'label' => Yii::t('youdate', 'People you like'),
                        'url' => ['/connections/likes', 'type' => LikeManager::TYPE_FROM_CURRENT_USER],
                        'icon' => 'user',
                        'count' => $counters[LikeManager::TYPE_FROM_CURRENT_USER],
                    ],
                    [
                        'label' => Yii::t('youdate', 'People who likes you'),
                        'url' => ['/connections/likes', 'type' => LikeManager::TYPE_TO_CURRENT_USER],
                        'icon' => 'user',
                        'count' => $counters[LikeManager::TYPE_TO_CURRENT_USER],
                    ],
                    [
                        'label' => Yii::t('youdate', 'Mutual likes'),
                        'url' => ['/connections/likes', 'type' => LikeManager::TYPE_MUTUAL],
                        'icon' => 'users',
                        'count' => $counters[LikeManager::TYPE_MUTUAL],
                    ],
                    [
                        'label' => Yii::t('youdate', 'Guests'),
                        'url' => ['/connections/guests'],
                        'icon' => 'eye',
                        'count' => $counters['guests'],
                    ],
                ],
            ]) ?>
            <?php if (isset($this->params['user.ads.hide']) && !$this->params['user.ads.hide']): ?>
                <div class="mt-2"><?= $this->themeSetting('adsSidebar') ?></div>
            <?php endif; ?>
        </div>
        <div class="col-lg-9 d-flex flex-column">
            <?= $content ?>
        </div>
    </div>
</div>
