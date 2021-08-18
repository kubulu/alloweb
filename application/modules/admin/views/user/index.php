<?php

use app\helpers\Html;
use app\models\User;
use yii\grid\GridView;
use yii\widgets\Pjax;

/** @var $this \yii\web\View */
/** @var $dataProvider \yii\data\ActiveDataProvider */
/** @var $searchModel \app\models\User */

$this->title = Yii::t('app', 'Manage users');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="nav-tabs-custom">
    <div class="tab-content no-padding">
        <?php Pjax::begin() ?>
        <div class="table-responsive">
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'layout' => "{items}\n{pager}",
                'tableOptions' => ['class' => 'table table-vcenter'],
                'columns' => [
                    [
                        'attribute' => 'username',
                        'label' => Yii::t('app', 'User'),
                        'format' => 'raw',
                        'value' => function(User $model) {
                            return $this->render('/partials/_user_column', [
                                'user' => $model,
                            ]);
                        }
                    ],
                    'email:email',
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
                        'attribute' => 'last_login_at',
                        'value' => function ($model) {
                            if (!$model->last_login_at || $model->last_login_at == 0) {
                                return Yii::t('app', 'Never');
                            } else if (extension_loaded('intl')) {
                                return Yii::t('app', '{0, date, MMMM dd, YYYY HH:mm}', [$model->last_login_at]);
                            } else {
                                return date('Y-m-d G:i:s', $model->last_login_at);
                            }
                        },
                    ],
                    [
                        'header' => Yii::t('app', 'Confirmation'),
                        'value' => function ($model) {
                            if ($model->isConfirmed) {
                                return '<div class="text-center">
                                <span class="text-success">' . Yii::t('app', 'Confirmed') . '</span>
                            </div>';
                            } else {
                                return Html::a(Yii::t('app', 'Confirm'), ['confirm', 'id' => $model->id], [
                                    'class' => 'btn btn-xs btn-success btn-block',
                                    'data-method' => 'post',
                                    'data-confirm' => Yii::t('app', 'Are you sure you want to confirm this user?'),
                                ]);
                            }
                        },
                        'format' => 'raw',
                    ],
                    [
                        'attribute' => 'is_verified',
                        'header' => Yii::t('app', 'Verification'),
                        'filter' => [
                            '1' => Yii::t('app', 'Yes'),
                            '0' => Yii::t('app', 'No')
                        ],
                        'value' => function (User $model) {
                            if ($model->profile->is_verified) {
                                return Html::tag('span', Yii::t('app', 'Yes'), [
                                    'class' => 'label label-primary'
                                ]);
                            }
                            return Html::tag('span', Yii::t('app', 'No'), [
                                'class' => 'label label-default'
                            ]);
                        },
                        'format' => 'raw',
                    ],
                    [
                        'attribute' => 'is_premium',
                        'header' => Yii::t('app', 'Premium'),
                        'filter' => [
                            '1' => Yii::t('app', 'Yes'),
                            '0' => Yii::t('app', 'No')
                        ],
                        'value' => function (User $model) {
                            if ($model->isPremium) {
                                return Html::tag('span', Yii::t('app', 'Yes'), [
                                    'class' => 'label label-primary'
                                ]);
                            }
                            return Html::tag('span', Yii::t('app', 'No'), [
                                'class' => 'label label-default'
                            ]);
                        },
                        'format' => 'raw',
                    ],
                    [
                        'header' => Yii::t('app', 'Block status'),
                        'value' => function ($model) {
                            if ($model->isBlocked) {
                                return Html::a(Yii::t('app', 'Unblock'), ['block', 'id' => $model->id], [
                                    'class' => 'btn btn-xs btn-success btn-block',
                                    'data-method' => 'post',
                                    'data-confirm' => Yii::t('app', 'Are you sure you want to unblock this user?'),
                                ]);
                            } else {
                                return Html::a(Yii::t('app', 'Block'), ['block', 'id' => $model->id], [
                                    'class' => 'btn btn-xs btn-danger btn-block',
                                    'data-method' => 'post',
                                    'data-confirm' => Yii::t('app', 'Are you sure you want to block this user?'),
                                ]);
                            }
                        },
                        'format' => 'raw',
                    ],
                    [
                        'class' => 'yii\grid\ActionColumn',
                        'template' => '{info} {delete}',
                        'buttons' => [
                            'info' => function ($url, $model, $key) {
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
                                    'data-confirm' => Yii::t('app', 'Are you sure want to delete this user?'),
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
