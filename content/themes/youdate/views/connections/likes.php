<?php

use youdate\widgets\DirectoryListView;

/** @var $dataProvider \yii\data\ActiveDataProvider */
/** @var $type string */
/** @var $this \app\base\View */
/** @var $counters array */

$this->title = Yii::t('youdate', 'Likes');
$this->context->layout = 'page-main';

$this->beginContent('@theme/views/connections/_layout.php', [
    'counters' => $counters,
]);

echo DirectoryListView::widget([
    'dataProvider' => $dataProvider,
    'itemView' => '_item',
    'itemOptions' => ['tag' => false],
    'emptyView' => '_empty_likes',
    'emptyViewParams' => [
        'type' => $type,
    ],
]);

$this->endContent();
