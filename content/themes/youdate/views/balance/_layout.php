<?php

use app\helpers\Html;

/* @var $this \app\base\View */
/* @var $currentBalance array */
/* @var $content string */

$this->context->layout = 'page-main';
$this->params['body.cssClass'] = 'body-balance-index';
?>
<div class="page-content">
    <div class="row">
        <div class="col-lg-3 mb-4">
            <h3 class="page-title mb-5"><?= Yii::t('youdate', 'Balance') ?></h3>
            <?= \youdate\widgets\Sidebar::widget([
                'items' => [
                    [
                        'label' => Yii::t('youdate', 'Services'),
                        'url' => ['/balance/services'],
                        'icon' => 'package',
                    ],
                    [
                        'label' => Yii::t('youdate', 'Transactions'),
                        'url' => ['/balance/transactions'],
                        'icon' => 'user',
                    ],
                    [
                        'label' => Yii::t('youdate', 'Buy credits'),
                        'url' => ['/balance/buy'],
                        'icon' => 'plus',
                    ],
                ],
            ]) ?>
        </div>
        <div class="col-lg-9">
            <div class="card mb-5 p-3 d-flex flex-row align-content-center justify-content-between">
                <div class="d-flex align-items-center">
                    <div class="stamp stamp-md bg-blue mr-3">
                        <i class="fa fa-money"></i>
                    </div>
                    <div>
                        <h4 class="m-0" style="margin-top: 2px !important;">
                            <?= $currentBalance ?> <small><?= Yii::t('youdate', 'credits') ?></small>
                        </h4>
                        <small class="text-muted"><?= Yii::t('youdate', 'current balance') ?></small>
                    </div>
                </div>
                <?php if (isset($showAddButton)): ?>
                    <div class="d-flex align-items-center">
                        <?= Html::a('<i class="fa fa-plus mr-2"></i>' . Yii::t('youdate', 'Buy'), ['buy'], [
                            'class' => 'btn btn-primary float-right',
                        ]) ?>
                    </div>
                <?php endif; ?>
            </div>
            <?= $content ?>
        </div>
    </div>
</div>
