<?php

use app\helpers\Html;
use app\models\Report;

?>
<div class="modal fade conversation-report" id="conversation-report" tabindex="-1"
     role="dialog" aria-labelledby="conversation-report-title" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="conversation-report-title">
                    <?= Yii::t('youdate', 'Report user') ?>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="pb-3"><strong><?= Yii::t('youdate', 'What is wrong with this user?') ?></strong></div>
                <?php foreach ((new Report())->getReportReasons() as $value => $title): ?>
                    <label class="custom-control custom-radio">
                        <?= Html::radio('reason', false, [
                            'class' => 'custom-control-input',
                            'value' => $value,
                            'ng-model' => 'reportReason',
                        ]) ?>
                        <span class="custom-control-label"><?= $title ?></span>
                    </label>
                <?php endforeach; ?>
                <div class="form-group">
                    <?= Html::textarea('description', '', [
                        'class' => 'form-control',
                        'ng-model' => 'reportDescription',
                        'placeholder' => Yii::t('youdate', 'Description'),
                    ]) ?>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <?= Yii::t('youdate', 'Cancel') ?>
                </button>
                <button type="button" class="btn btn-primary"
                        ng-disabled="!reportReason || !reportDescription"
                        ng-click="sendReport()">
                    <?= Yii::t('youdate', 'Report') ?>
                </button>
            </div>
        </div>
    </div>
</div>
