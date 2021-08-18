<div class="conversation-header bg-blue-lightest pl-5 pt-3 pb-3 pr-3" ng-show="currentContact != null" ng-cloak>
    <div class="row align-items-center justify-content-sm-start h-100">
        <div class="col-2 col-sm-2 col-md-2 col-lg-1" ng-show="!selectedMessages.length">
            <a class="avatar float-left"
               href="{{ currentContact.url }}"
               ng-style="{'background-image': 'url(' + currentContact.avatar + ')'}">
                <span class="avatar-status bg-green" ng-show="{{ currentContact.online }}"></span>
            </a>
        </div>
        <div class="col-4 col-sm-6 col-md-6 col-lg-7" ng-show="!selectedMessages.length">
            <div>
                <a href="{{ currentContact.url }} "><strong>{{ currentContact.fullName }}</strong></a>
                <div class="d-inline-block verified" ng-show="currentContact.verified"
                     rel="tooltip" title="<?= Yii::t('youdate', 'Verified user') ?>">
                    <i class="fe fe-check"></i>
                </div>
            </div>
            <div><small>{{ currentContact.username }}</small></div>
        </div>
        <div class="col-6 col-sm-8 col-md-8" ng-show="selectedMessages.length">
            <?= Yii::t('youdate', 'Selected messages') ?>: {{ selectedMessages.length }}
        </div>
        <div class="col-6 col-sm-4 col-md-4 text-right">
            <button class="btn btn-primary btn-sm d-md-none"
                    rel="tooltip"
                    title="<?= Yii::t('youdate', 'Toggle conversations') ?>"
                    ng-click="toggleConversations()">
                <i class="fe fe-users"></i>
            </button>
            <button class="btn btn-danger btn-sm delete-selected-messages"
                    ng-show="selectedMessages.length"
                    ng-click="deleteSelectedMessages()"
                    data-title="<?= Yii::t('youdate', 'Delete messages') ?>"
                    data-confirm-title="<?= Yii::t('youdate', 'Delete selected messages?') ?>"
                    data-confirm-button="<?= Yii::t('youdate', 'Delete') ?>"
                    data-cancel-button="<?= Yii::t('youdate', 'Cancel') ?>"
                    rel="tooltip"
                    title="<?= Yii::t('youdate', 'Delete') ?>">
                <i class="fe fe-trash"></i>
            </button>
            <div class="dropdown">
                <a class="btn btn-outline-primary btn-sm dropdown-toggle" href="#"
                   role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fe fe-menu"></i>
                </a>
                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuLink">
                    <a class="dropdown-item" href="#" data-toggle="modal" data-target="#conversation-report">
                        <?= Yii::t('youdate', 'Report') ?>
                    </a>
                    <a class="dropdown-item delete-conversation" href="#"
                       data-title="<?= Yii::t('youdate', 'Delete conversation') ?>"
                       data-confirm-title="<?= Yii::t('youdate', 'Delete this conversation?') ?>"
                       data-confirm-button="<?= Yii::t('youdate', 'Delete') ?>"
                       data-cancel-button="<?= Yii::t('youdate', 'Cancel') ?>"
                       ng-click="deleteConversation()">
                        <?= Yii::t('youdate', 'Delete this conversation') ?>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
