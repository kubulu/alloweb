<?php

/** @var $dataProvider \yii\data\ActiveDataProvider */
/** @var $this \app\base\View */
/** @var $profile \app\models\Profile */
/** @var $user \app\models\User */
/** @var $counters array */

$this->title = Yii::t('youdate', 'Encounters');
$this->context->layout = 'page-main-fill';
$this->params['body.cssClass'] = 'body-encounters d-block d-md-flex';
$this->params['pageWrapper.cssClass'] = 'd-block d-sm-flex min-h-100';
?>

<?php $this->beginContent('@theme/views/connections/_layout.php', ['counters' => $counters]) ?>

<h3 class="page-title mb-5"><?= Yii::t('youdate', 'Encounters') ?></h3>

<?= $this->render('_encounters', ['showQueue' => true]) ?>

<?php $this->endContent() ?>
