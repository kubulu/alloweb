<?php

use app\helpers\Url;
use app\helpers\Html;
use app\models\ProfileFieldCategory;

/** @var $this \yii\web\View */
/** @var $title string */
/** @var $model app\models\ProfileFieldCategory */

$this->title = Yii::t('app', 'Update Category "{0}"', $model->title);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Field categories'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="box">
    <div class="box-header clearfix">
        <?= Html::a('&larr; ' . Yii::t('app', 'Back to categories'),
            ['index'], ['class' => 'btn btn-default btn-sm pull-right']) ?>
    </div>
    <div class="box-body">
        <div class="profile-field-category-create row">
            <div class="col-xs-12 col-sm-6">
                <?= $this->render('_form', [
                    'model' => $model,
                ]) ?>
            </div>
        </div>
    </div>
</div>
