<?php

use app\helpers\Html;
use app\models\Report;

/** @var $user \app\models\User */
/** @var $profile \app\models\Profile */
/** @var $reportForm \app\forms\ReportForm */
?>
<div class="modal modal-form fade profile-report" id="profile-report" tabindex="-1"
     role="dialog" aria-labelledby="profile-title" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <?php $form = \youdate\widgets\ActiveForm::begin([
                'action' => ['/report/create'],
                'method' => 'post',
                'enableAjaxValidation' => false,
                'enableClientValidation' => true,
            ]) ?>
            <div class="modal-header">
                <h5 class="modal-title" id="profile-title">
                    <?= Yii::t('youdate', 'Report user') ?>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <?= $form->errorSummary($reportForm) ?>
                <div class="pb-3"><strong><?= Yii::t('youdate', 'What is wrong with this user?') ?></strong></div>
                <?= $form->field($reportForm, 'reason')->radioList($reportForm->getReportReasons(), [
                    'item' => function($index, $label, $name, $checked, $value) {
                        $return = '<label class="custom-control custom-radio">';
                        $return .= '<input type="radio" class="custom-control-input" name="' . $name . '" value="' . $value . '">';
                        $return .= '<div class="custom-control-label">' . Html::encode($label) . '</div>';
                        $return .= '</label>';
                        return $return;
                    }
                ]) ?>
                <?= Html::activeHiddenInput($reportForm, 'reportedUserId', ['value' => $user->id]) ?>
                <?= $form->field($reportForm, 'description')->textarea([
                    'class' => 'form-control',
                    'placeholder' => Yii::t('youdate', 'Description'),
                ]) ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary " data-dismiss="modal">
                    <?= Yii::t('youdate', 'Cancel') ?>
                </button>
                <button type="submit" class="btn btn-primary">
                    <?= Yii::t('youdate', 'Report') ?>
                </button>
            </div>
            <?php \youdate\widgets\ActiveForm::end() ?>
        </div>
    </div>
</div>
