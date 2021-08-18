<?php

use app\helpers\Url;
use app\helpers\Html;
use yii\widgets\ListView;

/** @var $model \app\models\Profile */
/** @var $form \yii\widgets\ActiveForm */
/** @var $this \app\base\View */
/** @var $dataProvider \yii\data\ActiveDataProvider */

$this->title = Yii::t('youdate', 'Photos');
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="card">
    <div class="card-header">
        <h3 class="card-title"><?= Html::encode($this->title) ?></h3>
        <div class="card-options">
            <a href="<?= Url::to(['/settings/upload']) ?>" class="btn btn-primary btn-sm">
                <i class="fe fe-plus"></i> <?= Yii::t('youdate', 'Upload') ?>
            </a>
        </div>
    </div>
    <div class="card-body">
        <?= $this->render('/_alert', ['module' => Yii::$app->getModule('user')]) ?>
        <?php \yii\widgets\Pjax::begin(['id' => 'pjax-settings-photos', 'linkSelector' => false]) ?>
            <?php if ($dataProvider->getTotalCount() > 0): ?>
                <?= ListView::widget([
                    'dataProvider' => $dataProvider,
                    'layout' => '{items} {pager}',
                    'options' => [
                        'class' => 'settings-photos row row-cards row-deck',
                    ],
                    'itemView' => '_photo',
                    'itemOptions' => [
                        'tag' => false,
                    ],
                ]) ?>
            <?php else: ?>
                <?= \youdate\widgets\EmptyState::widget([
                    'icon' => 'fe fe-image',
                    'subTitle' => Yii::t('youdate', 'You have no photos yet'),
                ]) ?>
            <?php endif; ?>
        <?php \yii\widgets\Pjax::end() ?>
    </div>
</div>
