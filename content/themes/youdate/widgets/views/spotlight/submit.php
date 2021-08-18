<?php

use app\helpers\Html;
use youdate\widgets\ActiveForm;

/** @var $user \app\models\User */
/** @var $profile \app\models\Profile */
/** @var $spotlightForm \app\forms\SpotlightForm */
/** @var $price int */
/** @var $userPhotos array */

?>
<div class="modal modal-form modal-spotlight-submit fade" id="spotlight-submit"
     tabindex="-1" role="dialog" aria-labelledby="spotlight-submit-title" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <?php $form = ActiveForm::begin([
                'id' => 'spotlight-submit-form',
                'action' => ['/balance/spotlight-submit'],
                'method' => 'post',
                'enableAjaxValidation' => false,
                'enableClientValidation' => true,
                'options' => ['data-pjax-container' => '#pjax-spotlight-horizontal'],
            ]); ?>
            <div class="modal-header">
                <h5 class="modal-title" id="spotlight-submit-title">
                    <?= Yii::t('youdate', 'Place your photo on spotlight') ?>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"></span>
                </button>
            </div>
            <div class="modal-body">
                <?= $form->errorSummary($spotlightForm) ?>
                <?= Html::activeHiddenInput($spotlightForm, 'userId') ?>

                <div class="form-group form-group-picker">
                    <div class="photo-picker row w-100 mx-0">
                        <?php if (count($userPhotos)): ?>
                            <?php foreach ($userPhotos as $photo): ?>
                                <label class="photo-item col-6 col-sm-4">
                                    <input type="radio" name="<?= Html::getInputName($spotlightForm, 'photoId') ?>" value="<?= $photo['id'] ?>" class="photo-input">
                                    <span class="photo" style="background-image: url('<?= $photo['url'] ?>')" ></span>
                                </label>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <?= \youdate\widgets\EmptyState::widget([
                                'icon' => 'fe fe-image',
                                'options' => ['class' => 'w-100'],
                                'title' => Yii::t('youdate', 'No photos'),
                                'subTitle' => Yii::t('youdate', 'You need at least one photo'),
                                'action' => Html::a('<i class="fa fa-image mr-2"></i>' . Yii::t('youdate', 'Upload'),
                                    ['/settings/upload'], [
                                        'class' => 'btn btn-primary',
                                    ]),
                            ]) ?>
                        <?php endif; ?>
                    </div>
                </div>

                <?= $form->field($spotlightForm, 'message')->textarea([
                    'class' => 'form-control',
                    'rows' => 2,
                    'placeholder' => Yii::t('youdate', 'Enter your message...'),
                ])->hint(Yii::t('youdate', 'Not required'), ['class' => 'text-small text-muted']) ?>

                <div class="text-green">
                    <?= Yii::t('youdate', 'Price: {0} credits', $price) ?>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <?= Yii::t('youdate', 'Cancel') ?>
                </button>
                <button type="submit" class="btn btn-primary">
                    <?= Yii::t('youdate', 'Place photo') ?>
                </button>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
