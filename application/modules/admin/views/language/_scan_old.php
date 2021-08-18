<?php

use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this \yii\web\View */
/* @var $oldDataProvider \yii\data\ArrayDataProvider */
?>
<?php if ($oldDataProvider->totalCount > 1) : ?>
    <?= Html::button(Yii::t('app', 'Select all'), ['id' => 'select-all', 'class' => 'btn btn-primary']) ?>
    <?= Html::button(Yii::t('app', 'Delete selected'), ['id' => 'delete-selected', 'class' => 'btn btn-danger']) ?>
<?php endif ?>

<?php if ($oldDataProvider->totalCount > 0) : ?>
    <div class="table-responsive">
        <?= GridView::widget([
            'id' => 'delete-source',
            'dataProvider' => $oldDataProvider,
            'tableOptions' => ['class' => 'table table-vcenter'],
            'columns' => [
                [
                    'format' => 'raw',
                    'attribute' => '#',
                    'content' => function ($languageSource) {
                        return Html::checkbox('LanguageSource[]', false, [
                            'value' => $languageSource['id'], 'class' => 'language-source-cb'
                        ]);
                    },
                ],
                'id',
                'category',
                'message',
                'languages',
                [
                    'format' => 'raw',
                    'attribute' => Yii::t('app', 'Action'),
                    'content' => function ($languageSource) {
                        return Html::a(Yii::t('app', 'Delete'), Url::to(['delete-source']), [
                                'data-id' => $languageSource['id'], 'class' => 'delete-item btn btn-xs btn-danger']
                        );
                    },
                ],
            ],
        ]) ?>
    </div>
<?php endif ?>
