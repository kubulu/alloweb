<?php

use yii\helpers\ArrayHelper;

/** @var $this \app\base\View */
$currentUserPhotoUrl = ArrayHelper::getValue($this->params, 'user.avatar');
?>
<script type="text/ng-template" id="encounters-mutual.html">
    <div class="modal-body encounter-modal-body" id="modal-body">
        <h4 class="text-center mt-4 mb-6 pb-5 border-bottom">
            <?= Yii::t('youdate', 'Congratulations! {{ previousEncounter.profile.displayName }} likes you too!') ?>
        </h4>
        <div class="encounters-mutual d-flex justify-content-center align-items-center">
            <div class="avatar avatar-xl" style="background-image: url('<?= $currentUserPhotoUrl ?>')"></div>
            <div class="heart px-5">
                <i class="fa fa-heart"></i>
            </div>
            <div class="avatar avatar-xl" style="background-image: url('{{ previousEncounter.profile.avatar }}')"></div>
        </div>
    </div>
    <div class="modal-footer">
        <a ng-href="{{ previousEncounter.profile.url }}" class="btn btn-primary" role="button">
            <?= Yii::t('youdate', 'Visit profile') ?>
        </a>
        <button class="btn btn-secondary" type="button" ng-click="$ctrl.close()">
            <?= Yii::t('youdate', 'Close') ?>
        </button>
    </div>
</script>
