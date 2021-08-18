<?php

use app\helpers\Html;
use app\models\Log;
use yii\log\Logger;
use yii\grid\GridView;
use yii\widgets\Pjax;

/** @var $this \yii\web\View */
/** @var $logDataProvider \yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Manage logs');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="actions">
    <?= Html::a('<i class="fa fa-trash"></i> ' . Yii::t('app', 'Delete all messages'), ['flush'], [
        'class' => 'btn btn-sm btn-primary',
        'data-method' => 'post',
        'data-confirm' => Yii::t('app', 'Are you sure you want to delete all log messages?'),
    ]) ?>
</div>
<div class="nav-tabs-custom">
    <div class="tab-content no-padding">
        <?php Pjax::begin() ?>
        <div class="table-responsive">
            <?= GridView::widget([
                'dataProvider' => $logDataProvider,
                'layout' => "{items}\n{pager}",
                'tableOptions' => ['class' => 'table'],
                'columns' => [
                    'id',
                    [
                        'attribute' => 'level',
                        'format' => 'raw',
                        'value' => function (Log $log) {
                            switch ($log->level) {
                                case Logger::LEVEL_ERROR:
                                    $class = 'danger';
                                    $label = 'error';
                                    break;
                                case Logger::LEVEL_WARNING:
                                    $class = $label = 'warning';
                                    break;
                                case Logger::LEVEL_INFO:
                                    $class = $label = 'info';
                                    break;
                                default:
                                    $class = 'default';
                                    $label = 'other';
                                    break;
                            }
                            return Html::tag('span', $label, ['class' => 'label label-' . $class]);
                        }
                    ],
                    [
                        'attribute' => 'message',
                        'format' => 'raw',
                        'value' => function(Log $log) {
                            return Html::tag('code', $log->category) . Html::tag('div', strtok($log->message, "\n"));
                        }
                    ],
                    [
                        'attribute' => 'log_time',
                        'format' => 'datetime',
                        'contentOptions' => ['width' => 100],
                    ],
                    [
                        'class' => \yii\grid\ActionColumn::class,
                        'template' => '{view} {delete}',
                        'contentOptions' => ['width' => 100],
                        'buttons' => [
                            'view' => function ($url, $model, $key) {
                                return Html::a('<span class="fa fa-eye"></span>', $url, [
                                    'title' => Yii::t('app', 'View'),
                                    'data-pjax' => 0,
                                    'class' => 'btn btn-sm btn-primary',
                                ]);
                            },
                            'delete' => function ($url, $model, $key) {
                                return Html::a('<span class="fa fa-trash"></span>', $url, [
                                    'title' => Yii::t('app', 'Delete'),
                                    'data-pjax' => 0,
                                    'data-method' => 'post',
                                    'data-confirm' => Yii::t('app', 'Are you sure want to delete log message?'),
                                    'class' => 'btn btn-sm btn-danger',
                                ]);
                            },
                        ]
                    ],
                ],
            ]); ?>
        </div>
        <?php Pjax::end() ?>
    </div>
</div>
