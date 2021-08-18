<?php

use app\helpers\Html;
use app\helpers\Url;

/** @var $this \yii\web\View */
/** @var $title string */
/** @var $model app\models\ProfileField */
/** @var $categories app\models\ProfileFieldCategory[] */
/** @var $fieldInstance \app\models\fields\BaseType */
/** @var $fieldClasses array */
/** @var $fieldClass string */

$this->title = Yii::t('app', 'New field');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Profile fields'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="box">
    <div class="box-header with-border">
        &nbsp;
        <div class="box-tools pull-right">
            <?= Html::a('&larr; ' . Yii::t('app', 'Back to fields'),
                ['index'], ['class' => 'btn btn-default btn-sm']) ?>
        </div>
    </div>
    <div class="box-body">
        <div class="profile-field-category-create">
            <?= $this->render('_form', [
                'model' => $model,
                'categories' => $categories,
                'fieldInstance' => $fieldInstance,
                'fieldClasses' => $fieldClasses,
                'fieldClass' => $fieldClass,
                'formAction' => Url::to(!empty($fieldClass) ? ['create', 'fieldClass' => $fieldClass] : ['create']),
            ]) ?>
        </div>
    </div>
</div>
