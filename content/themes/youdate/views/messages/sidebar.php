<div class="messages-conversations-filter pt-2 pl-2 pr-2">
    <div class="input-icon mb-3">
        <input type="text" class="form-control"
               ng-model="conversationsQuery"
               ng-change="getConversations()"
               ng-model-options="{debounce: 750}"
               ng-bind="conversationsQuery"
               placeholder="<?= Yii::t('youdate', 'Search contact') ?>">
        <span class="input-icon-addon"><i class="fe fe-search"></i></span>
    </div>
</div>
<div class="messages-conversations" ng-show="hasContacts()">
    <div class="wrapper-conversations" lazy-img-container>
        <div class="conversations list-group list-group-flush">
            <a href="#"
               ng-cloak ng-repeat="(key, item) in conversations"
               ng-click="setCurrentContactId(item.contact.id, key)"
               class="conversation list-group-item list-group-item-action flex-column align-items-start
                {{item.contact.premium != false ? 'premium ' : ''}}
                {{item.contact.id == currentContactId ? 'active' : ''}}">
                <div class="d-flex w-100 justify-content-start align-content-center">
                    <div class="pr-3 align-self-center">
                        <div class="avatar" ng-style="{'background-image': 'url(' + item.contact.avatar + ')'}">
                            <span class="avatar-status bg-green" ng-show="{{ item.contact.online }}"></span>
                        </div>
                    </div>
                    <div class="align-self-center">
                        <h5 class="name mb-1">{{ item.contact.full_name }}</h5>
                        <small class="message">{{ item.last_message.text }}</small>
                    </div>
                    <div class="align-self-center ml-auto" ng-show="newMessagesCounts[item.contact.id]">
                        <span class="badge badge-success badge-pill mr-auto">{{ newMessagesCounts[item.contact.id].new_messages_count }}</span>
                    </div>
                </div>
            </a>
        </div>
    </div>
</div>
<div class="no-conversations-found" ng-show="conversationsQuery.length > 0 && !hasContacts()">
    <div class="text-muted text-center p-2">
        <?= Yii::t('youdate', 'Conversations not found') ?>
    </div>
</div>
