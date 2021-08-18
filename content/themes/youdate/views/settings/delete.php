<?php

use app\helpers\Html;

/** @var $this \app\base\View */
/** @var $blockedUsers \app\models\Block[] */

$this->title = Yii::t('youdate', 'Delete account');
$this->params['breadcrumbs'][] = $this->title;
$premiumFeaturesEnabled = \yii\helpers\ArrayHelper::getValue($this->params, 'site.premiumFeatures.enabled');
?>
<div class="card">
    <div class="card-header">
        <h3 class="card-title"><?= Html::encode($this->title) ?></h3>
    </div>
    <div class="card-body">
        <?= $this->render('/_alert') ?>
        <div class="alert alert-danger">
            <strong><?= Yii::t('youdate', 'Warning') ?>:</strong>
            <?= Yii::t('youdate', 'This action will remove all your data completely.') ?>
        </div>
        <div class="text-wrap pt-2 mb-5">
            <?= Yii::t('youdate', 'Data to be removed') ?>:
            <ul class="pt-2">
                <li><?= Yii::t('youdate', 'Your profile info') ?></li>
                <li><?= Yii::t('youdate', 'Your photos') ?></li>
                <li><?= Yii::t('youdate', 'Your messages') ?></li>
                <li><?= Yii::t('youdate', 'Your likes and connections') ?></li>
                <?php if ($premiumFeaturesEnabled): ?>
                <li><?= Yii::t('youdate', 'Your balance and transactions') ?></li>
                <?php endif; ?>
            </ul>
        </div>
        <div class="pt-2">
            <?= Html::a(Yii::t('youdate', 'Delete account'), ['/settings/delete'], [
                'class' => 'btn btn-danger btn-lg',
                'data-method' => 'post',
                'data-confirm' => Yii::t('youdate', 'Are you sure you want to delete your profile?'),
            ]) ?>
        </div>
    </div>
</div>
