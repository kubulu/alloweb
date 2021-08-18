<?php

/* @var $this yii\web\View */
/* @var $newDataProvider \yii\data\ArrayDataProvider */
/* @var $oldDataProvider \yii\data\ArrayDataProvider */

$this->title = Yii::t('app', 'Scanning project');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Languages'), 'url' => ['list']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="box box-solid">
    <div class="box-body">
        <div id="w2-info" class="alert-info alert fade in">
            <?= Yii::t('app', '{n, plural, =0{No new entries} =1{One new entry} other{# new entries}} were added!', [
                'n' => $newDataProvider->totalCount
            ]) ?>
        </div>

        <?= $this->render('_scan_new', ['newDataProvider' => $newDataProvider]) ?>

        <div id="w2-danger" class="alert-danger alert fade in">
            <?= Yii::t('app', '{n, plural, =0{No entries} =1{One entry} other{# entries}} remove!', [
                'n' => $oldDataProvider->totalCount
            ]) ?>
        </div>

        <?= $this->render('_scan_old', ['oldDataProvider' => $oldDataProvider]) ?>
    </div>
</div>
