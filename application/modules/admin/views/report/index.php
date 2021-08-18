<?php

use yii\grid\GridView;
use yii\helpers\Html;
use app\modules\admin\models\Report;
use dosamigos\grid\columns\ToggleColumn;

/** @var $this \yii\web\View */
/** @var $type string */
/** @var $dataProvider \yii\data\ActiveDataProvider */
/** @var $searchModel \app\modules\admin\models\Photo */

$this->title = Yii::t('app', 'Manage reports');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="filters">
    <?= Html::a(Yii::t('app', 'New reports'), ['index', 'type' => Report::TYPE_NEW],
        ['class' => 'btn btn-sm ' . ($type == Report::TYPE_NEW ? 'btn-primary' : 'btn-default')]) ?>
    <?= Html::a(Yii::t('app', 'All reports'), ['index', 'type' => Report::TYPE_VIEWED],
        ['class' => 'btn btn-sm ' . ($type == Report::TYPE_VIEWED ? 'btn-primary' : 'btn-default')]) ?>
</div>
<div class="nav-tabs-custom">
    <div class="tab-content no-padding table-responsive">
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'layout' => "{summary}\n{items}\n{pager}",
            'tableOptions' => ['class' => 'table table-vcenter'],
            'columns' => [
                [
                    'attribute' => 'from_user_id',
                    'format' => 'raw',
                    'value' => function(Report $model) {
                        return $this->render('/partials/_user_column', [
                            'user' => $model->fromUser,
                        ]);
                    }
                ],
                [
                    'attribute' => 'reported_user_id',
                    'format' => 'raw',
                    'value' => function(Report $model) {
                        return $this->render('/partials/_user_column', [
                            'user' => $model->reportedUser,
                        ]);
                    }
                ],
                [
                    'attribute' => 'reason',
                    'format' => 'raw',
                    'value' => function(Report $model) {
                        return $model->getReasonLabel();
                    },
                ],
                [
                    'attribute' => 'description',
                    'format' => 'ntext',
                ],
                [
                    'attribute' => 'created_at',
                    'value' => function ($model) {
                        if (extension_loaded('intl')) {
                            return Yii::t('app', '{0, date, MMMM dd, YYYY HH:mm}', [$model->created_at]);
                        } else {
                            return date('Y-m-d G:i:s', $model->created_at);
                        }
                    },
                ],
                [
                    'class' => ToggleColumn::class,
                    'attribute' => 'is_viewed',
                    'onLabel' => Yii::t('app', 'Viewed'),
                    'offLabel' => Yii::t('app', 'Not viewed'),
                    'onIcon' => 'fa fa-check-square-o',
                    'offIcon' => 'fa fa-square-o',
                    'contentOptions' => ['class' => 'text-center'],
                    'filter' => [
                        Report::IS_NEW => Yii::t('app', 'New'),
                        Report::IS_VIEWED => Yii::t('app', 'Viewed'),
                    ]
                ],
                [
                    'class' => 'yii\grid\ActionColumn',
                    'template' => '{delete}',
                ],
            ],
        ]); ?>
    </div>
</div>
