<?php

/* @var $this yii\web\View */
/* @var $model \app\models\Language */

$this->title = Yii::t('app', 'Update language');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Manage languages'), 'url' => ['list']];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="box box-solid">
    <div class="box-body">
        <div class="language-update">
            <?= $this->render('_form', [
                'model' => $model,
            ]) ?>
        </div>
    </div>
</div>
