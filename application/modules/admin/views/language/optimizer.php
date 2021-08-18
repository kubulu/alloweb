<?php

/* @var $this \yii\web\View */
/* @var $newDataProvider \yii\data\ArrayDataProvider */

$this->title = Yii::t('app', 'Optimise database');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Manage languages'), 'url' => ['list']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="box box-solid">
    <div class="box-body">
        <div id="w2-info" class="alert-info alert fade in">
            <?= Yii::t('app', '{n, plural, =0{No entries} =1{One entry} other{# entries}} were removed!', [
                'n' => $newDataProvider->totalCount
            ]) ?>
        </div>
        <?= $this->render('_scan_new', [
            'newDataProvider' => $newDataProvider,
        ]) ?>
    </div>
</div>
