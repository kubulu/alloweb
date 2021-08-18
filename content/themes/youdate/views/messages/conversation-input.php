<?php

use yii\helpers\ArrayHelper;
use app\helpers\Html;

/** @var $this \app\base\View */
/** @var $this \app\base\View */
?>
<div class="conversation-input pl-5 pt-3 pb-3 pr-5" ng-show="currentContact != null" ng-cloak>
    <script type="text/ng-template" id="'templates/popover/popover-template.html'">
        <div class="popover bs-popover-top" role="tooltip">
            <div class="arrow"></div>
            <h3 class="popover-header" ng-bind="uibTitle" ng-if="uibTitle"></h3>
            <div class="popover-body"
                 uib-tooltip-template-transclude="contentExp()"
                 tooltip-template-transclude-scope="originScope()">
            </div>
        </div>
    </script>
    <script type="text/ng-template" id="emoji.html">
        <div class="emoji-items">
            <?php foreach (ArrayHelper::getValue($this->params, 'site.emoji', []) as $emoji): ?>
                <div class="emoji-item" ng-click="addEmoji('<?= $emoji ?>')">
                    <?= Html::encode($emoji) ?>
                </div>
            <?php endforeach; ?>
        </div>
    </script>
    <div class="input-group">
        <input ng-model="message" type="text" class="form-control message-input" my-enter="sendMessage()"
               placeholder="<?= Yii::t('youdate', 'Enter your message...') ?>">
        <div class="input-group-append">
            <button class="btn btn-secondary btn-emoji-picker"
                    uib-popover-template="'emoji.html'"
                    popover-placement="top-right"
                    popover-class="popover-emoji bs-popover-top"
                    popover-popup-close-delay="1"
                    popover-trigger="'outsideClick'">
                <i class="fa fa-smile-o"></i>
            </button>
        </div>
        <div class="input-group-append">
            <button type="button" class="btn btn-secondary" ng-click="sendMessage()" ng-disabled="!message">
                <i class="fe fe-send"></i>
            </button>
        </div>
    </div>
</div>
