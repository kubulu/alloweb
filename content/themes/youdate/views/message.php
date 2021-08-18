<?php

use app\helpers\Html;

/** @var $this \app\base\View */

$this->context->layout = '//page-single';
?>
<?= $this->render('/partials/auth-header.php') ?>
<div class="row justify-content-center">
    <div class="card col-11 col-sm-10 col-md-8">
        <div class="card-header">
            <h3 class="card-title">
                <?= Html::encode($title) ?>
            </h3>
        </div>
        <div class="card-body">
            <?php foreach (Yii::$app->session->getAllFlashes() as $type => $message): ?>
                <?php if (in_array($type, ['success', 'danger', 'warning', 'info'])): ?>
                    <div class="alert alert-<?= $type ?> alert-dismissible">
                        <?= Html::encode($message) ?>
                    </div>
                <?php endif ?>
            <?php endforeach ?>
            <div>
                <?= Html::a(Yii::t('youdate', 'Go back'), ['/'], ['class' => 'btn btn-primary']) ?>
                <?= Html::a(Yii::t('youdate', 'Sign in'), ['/security/login'], ['class' => 'btn btn-secondary']) ?>
            </div>
        </div>
    </div>
</div>
