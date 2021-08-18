<?php

use app\models\BalanceTransaction;
use youdate\helpers\HtmlHelper;
use youdate\widgets\GridView;

/** @var $dataProvider \yii\data\ActiveDataProvider */
/** @var $this \app\base\View */
/** @var $currentBalance integer */

$this->title = Yii::t('youdate', 'Balance');
?>

<?php $this->beginContent('@theme/views/balance/_layout.php', [
    'currentBalance' => $currentBalance,
    'showAddButton' => true,
]) ?>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <?= Yii::t('youdate', 'Transactions') ?>
        </h3>
    </div>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'emptyView' => '_empty',
        'showHeader' => false,
        'layout' => '<div class="table-responsive">{items}</div> {pager}',
        'options' => ['tag' => false],
        'summaryOptions' => ['class' => 'summary p-2'],
        'tableOptions' => ['class' => 'table table-outline table-vcenter text-nowrap card-table transactions-grid-view'],
        'columns' => [
            'transactionType' => [
                'format' => 'raw',
                'contentOptions' => ['class' => 'text-center col-transaction-type'],
                'value' => function (BalanceTransaction $model) {
                    return HtmlHelper::transactionIcon($model->getTransactionInfo());
                }
            ],
            'transactionTitle' => [
                'format' => 'raw',
                'contentOptions' => ['class' => 'expand'],
                'value' => function(BalanceTransaction $model) {
                    $info = $model->getTransactionInfo();
                    if ($info) {
                        return $info->getTitle();
                    } else {
                        return 'unknown';
                    }
                }
            ],
            'transactionAmount' => [
                'format' => 'raw',
                'attribute' => 'amount',
                'contentOptions' => ['class' => 'text-right'],
                'value' => function (BalanceTransaction $model) {
                    if ($model->amount > 0) {
                        return sprintf("<span class=\"transaction-add\">%+d %s</span>",
                            $model->amount,
                            Yii::t('youdate', 'credits')
                        );
                    } else {
                        return sprintf("<span class=\"transaction-remove\">%+d %s</span>",
                            $model->amount,
                            Yii::t('youdate', 'credits')
                        );
                    }
                }
            ],
            'transactionService' => [
                'format' => 'raw',
                'contentOptions' => ['class' => 'text-right'],
                'value' => function (BalanceTransaction $model) {
                    $info = $model->getTransactionInfo();
                    if ($info && $info->getServiceName()) {
                        return '<i class="payment payment-' . $info->getServiceName() . '"></i>';
                    } else {
                        return '';
                    }
                }
            ],
            'transactionDate' => [
                'attribute' => 'created_at',
                'format' => 'datetime',
                'contentOptions' => ['class' => 'text-muted text-center shrink'],
            ],
        ]
    ]) ?>
</div>

<?php $this->endContent() ?>
