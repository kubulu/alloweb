<?php

use app\helpers\Html;
use youdate\widgets\ActiveForm;

/** @var $user \app\models\User */
/** @var $profile \app\models\Profile */
/** @var $newMessageForm \app\forms\MessageForm */

?>
<div class="modal modal-form fade" id="profile-new-message"
     tabindex="-1" role="dialog" aria-labelledby="profile-new-message-title" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <?php $form = ActiveForm::begin([
                'id' => 'new-message-form',
                'action' => ['/messages/create'],
                'method' => 'post',
                'enableAjaxValidation' => false,
                'enableClientValidation' => true,
            ]); ?>
                <div class="modal-header">
                    <h5 class="modal-title placeholder-title" id="profile-new-message-title">
                        <?php if (isset($profile)): ?>
                            <?= Yii::t('youdate', 'Message to {0}', [Html::encode($profile->getDisplayName())]) ?>
                        <?php endif; ?>
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true"></span>
                    </button>
                </div>
                <div class="modal-body">
                    <?= $form->errorSummary($newMessageForm) ?>
                    <?= Html::activeHiddenInput($newMessageForm, 'contactId', [
                        'value' => isset($user) ? $user->id : null,
                        'class' => 'placeholder-contact-id',
                    ]) ?>
                    <?= $form->field($newMessageForm, 'message')->textarea([
                        'class' => 'form-control',
                        'rows' => 3,
                        'placeholder' => Yii::t('youdate', 'Enter your message...'),
                    ])->label(false) ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <?= Yii::t('youdate', 'Cancel') ?>
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-send"></i> <?= Yii::t('youdate', 'Send message') ?>
                    </button>
                </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
