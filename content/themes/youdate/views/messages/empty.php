<?php

use app\helpers\Html;

?>
<div class="row align-self-center h-100 ng-hide"
     ng-show="initialStateLoaded === true && !hasContacts() && !conversationsQuery.length">
    <div class="no-contacts align-self-center m-auto p-5">
        <div class="text-center">
            <h4 class="text-gray-dark"><?= Yii::t('youdate', 'No contacts') ?></h4>
            <p class="text-gray">
                <?= Yii::t('youdate', 'You don\'t have any conversations yet') ?>
            </p>
            <?= Html::a(Yii::t('youdate', 'Browse people'), ['/directory/index'], [
                'class' => 'btn btn-primary',
            ]) ?>
        </div>
    </div>
</div>
