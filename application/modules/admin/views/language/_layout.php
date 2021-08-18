<?php

use app\helpers\Html;
use rmrevin\yii\fontawesome\FA;
use yii\bootstrap\Nav;

/** @var $content string */
/** @var $this \app\base\View */

$this->beginContent('@app/modules/admin/views/layouts/main.php');
?>

<div class="box box-solid">
    <div class="box-body no-padding clearfix">
        <div class="row">
            <div class="col-xs-12 col-sm-6">
                <?= Nav::widget([
                    'options' => [
                        'class' => 'nav-pills pull-left',
                    ],
                    'items' => [
                        [
                            'label' => Yii::t('app', 'Languages'),
                            'url' => ['list']
                        ],
                        [
                            'label' => Yii::t('app', 'Import'),
                            'url' => ['import']
                        ],
                        [
                            'label' => Yii::t('app', 'Export'),
                            'url' => ['export']
                        ],
                    ]
                ]) ?>
            </div>
            <div class="col-xs-12 col-sm-6">
                <div class="language-actions">
                    <?= Html::a('<i class="fa fa-plus"></i> ' . Yii::t('app', 'Create'), ['create'], ['class' => 'btn btn-primary btn-sm']) ?>
                    <?= Html::a('<i class="fa fa-search"></i> ' . Yii::t('app', 'Scan'), ['scan'], ['class' => 'btn btn-primary btn-sm']) ?>
                    <?= Html::a('<i class="fa fa-cogs"></i> ' . Yii::t('app', 'Optimize'), ['optimizer'], ['class' => 'btn btn-primary btn-sm']) ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $content ?>

<?php $this->endContent() ?>
