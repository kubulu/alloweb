<?php

use youdate\widgets\DirectoryListView;

/** @var $dataProvider \yii\data\ActiveDataProvider */
/** @var $type string */
/** @var $this \app\base\View */
/** @var $counters array */

$this->title = Yii::t('youdate', 'Guests');
$this->context->layout = 'page-main';

$this->beginContent('@theme/views/connections/_layout.php', [
    'counters' => $counters,
]);

echo DirectoryListView::widget([
    'dataProvider' => $dataProvider,
    'itemView' => '_item_guest',
    'itemOptions' => ['tag' => false],
    'emptyView' => '_empty_guests',
    'emptyViewParams' => [
    ],
]);

$this->endContent();
