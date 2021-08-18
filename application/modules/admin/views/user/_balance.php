<?php

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

/** @var $this \yii\web\View */
/** @var $user \app\models\User */
/** @var $profile \app\models\Profile */
/** @var $balanceForm \app\modules\admin\forms\BalanceUpdateForm */
/** @var $currentBalance integer */
?>

<?php $this->beginContent('@app/modules/admin/views/user/update.php', ['user' => $user]) ?>

<div class="box box-default">
    <div class="box-header with-border">
        <h2 class="box-title"><?= Yii::t('app', 'Edit balance') ?></h2>
    </div>
    <div class="box-body">

        <div class="alert alert-info">
            <?= Yii::t('app', 'Current balance: {0} credits', $currentBalance) ?>
        </div>
        <?php $form = ActiveForm::begin([
            'layout' => 'horizontal',
            'enableAjaxValidation' => true,
            'enableClientValidation' => false,
            'fieldConfig' => [
                'horizontalCssClasses' => [
                    'wrapper' => 'col-sm-9',
                ],
            ],
        ]); ?>

        <?= $form->errorSummary($balanceForm) ?>

        <?= $form->field($balanceForm, 'amount') ?>
        <?= $form->field($balanceForm, 'notes') ?>

        <div class="form-group">
            <div class="col-lg-offset-3 col-lg-9">
                <?= Html::submitButton(Yii::t('app', 'Add credits'), ['class' => 'btn btn-block btn-primary']) ?>
            </div>
        </div>

        <?php ActiveForm::end(); ?>
    </div>
</div>

<?php $this->endContent() ?>
