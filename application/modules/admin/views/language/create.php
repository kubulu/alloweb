<?php

/* @var $this yii\web\View */
/* @var $model \app\models\Language */

$this->title = Yii::t('app', 'Add language');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Manage languages'), 'url' => ['list']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="box box-solid">
    <div class="box-body">
        <div class="language-create">
            <?= $this->render('_form', [
                'model' => $model,
            ]) ?>
        </div>
    </div>
</div>
