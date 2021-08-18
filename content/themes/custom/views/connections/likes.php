<?php

use youdate\widgets\DirectoryListView;

/** @var $dataProvider \yii\data\ActiveDataProvider */
/** @var $type string */
/** @var $this \app\base\View */
/** @var $counters array */

$this->title = Yii::t('youdate', 'Likes');
$this->context->layout = 'page-main';

$this->beginContent('@extendedTheme/views/connections/_layout_connections.php', [
    'counters' => $counters,
]);

echo DirectoryListView::widget([
    'dataProvider' => $dataProvider,
    'itemView' => '@theme/views/connections/_item',
    'itemOptions' => ['tag' => false],
    'emptyView' => '@theme/views/connections/_empty_likes',
    'emptyViewParams' => [
        'type' => $type,
    ],
]);

$this->endContent();
