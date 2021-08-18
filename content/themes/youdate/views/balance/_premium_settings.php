<?php

use app\helpers\Html;

/** @var $premiumSettings \app\forms\PremiumSettingsForm */

?>
<div class="premium-settings mt-6">
    <h4><?= Yii::t('youdate', 'Premium settings') ?></h4>
    <?php $form = \youdate\widgets\ActiveForm::begin(['action' => ['premium-settings'], 'method' => 'post']) ?>

    <?= $form->field($premiumSettings, 'incognitoActive')->checkbox() ?>

    <?= $form->field($premiumSettings, 'showOnlineStatus')->checkbox() ?>

    <?= Html::button(Yii::t('youdate', 'Save'), [
        'class' => 'btn btn-primary',
        'type' => 'submit',
    ]) ?>

    <?php \youdate\widgets\ActiveForm::end() ?>
</div>
