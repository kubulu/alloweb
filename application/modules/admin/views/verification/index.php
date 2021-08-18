<?php

use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\ListView;
use app\modules\admin\models\Verification;

/** @var $this \yii\web\View */
/** @var $type string */
/** @var $dataProvider \yii\data\ActiveDataProvider */
/** @var $searchModel \app\modules\admin\models\Photo */

$this->title = Yii::t('app', 'Manage verifications');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="filters">
    <?= Html::a(Yii::t('app', 'New verifications'), ['index', 'type' => Verification::TYPE_NEW],
        ['class' => 'btn btn-sm ' . ($type == Verification::TYPE_NEW ? 'btn-primary' : 'btn-default')]) ?>
    <?= Html::a(Yii::t('app', 'Approved verifications'), ['index', 'type' => Verification::TYPE_APPROVED],
        ['class' => 'btn btn-sm ' . ($type == Verification::TYPE_APPROVED ? 'btn-primary' : 'btn-default')]) ?>
</div>
<div class="nav-tabs-custom">
    <div class="tab-content">
        <div class="tab-content">
            <?php \yii\widgets\Pjax::begin(['id' => 'pjax-list-view']) ?>
            <?= ListView::widget([
                'dataProvider' => $dataProvider,
                'layout' => "{summary}\n<div class=\"clearfix\">{items}</div>\n{pager}",
                'options' => ['class' => 'list-view photos-list-view clearfix'],
                'itemView' => '_item',
                'itemOptions' => ['tag' => false],
            ]); ?>
            <?php \yii\widgets\Pjax::end() ?>
        </div>
    </div>
</div>
