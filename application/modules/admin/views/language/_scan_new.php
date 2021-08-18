<?php

/* @var $this \yii\web\View */
/* @var $newDataProvider \yii\data\ArrayDataProvider */

use yii\grid\GridView;

?>
<?php if ($newDataProvider->totalCount > 0) : ?>
    <div class="table-responsive">
        <?= GridView::widget([
            'id' => 'added-source',
            'dataProvider' => $newDataProvider,
            'tableOptions' => ['class' => 'table table-vcenter'],
            'columns' => [
                ['class' => \yii\grid\SerialColumn::class],
                'category',
                'message',
            ],
        ])?>
    </div>
<?php endif ?>
