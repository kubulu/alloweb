<?php

use youdate\widgets\Connect;
use yii\helpers\Html;

/** @var $this \app\base\View */
/** @var $form \yii\widgets\ActiveForm */
/** @var $user \app\models\User */

$this->title = Yii::t('youdate', 'Networks');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="card">
    <div class="card-header">
        <h3 class="card-title"><?= Html::encode($this->title) ?></h3>
    </div>
    <div class="card-body">
        <?= $this->render('/_alert', ['module' => Yii::$app->getModule('user')]) ?>
        <div class="alert alert-info alert-icon">
            <i class="fe fe-info mr-2" aria-hidden="true"></i>
            <?= Yii::t('youdate', 'You can connect multiple accounts to be able to log in using them') ?>.
        </div>
        <?php $auth = Connect::begin([
            'baseAuthUrl' => ['/security/auth'],
            'accounts' => $user->accounts,
            'autoRender' => false,
            'popupMode' => false,
        ]) ?>
        <?php foreach ($auth->getClients() as $client): ?>
        <div class="row d-flex align-items-center pb-3">
            <div class="col d-flex align-items-center">
                <span class="d-inline float-left pr-5">
                    <button type="button" class="btn btn-icon btn-<?= $client->getName() ?>">
                        <i class="fa fa-<?= $client->getName() ?>"></i>
                    </button>
                </span>
                <strong><?= $client->getTitle() ?></strong>
            </div>
            <div class="col">
                <?= $auth->isConnected($client) ?
                    Html::a(Yii::t('youdate', 'Disconnect'), $auth->createClientUrl($client), [
                        'class' => 'btn btn-danger float-right',
                        'data-method' => 'post',
                    ]) :
                    Html::a(Yii::t('youdate', 'Connect'), $auth->createClientUrl($client), [
                        'class' => 'btn btn-primary float-right',
                    ])
                ?>
            </div>
        </div>
        <?php endforeach; ?>
        <?php Connect::end() ?>
    </div>
</div>
