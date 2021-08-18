<div class="profile-info ng-hide" ng-show="initialStateLoaded && hasEncounters()">
    <div class="profile-info-block profile-main-info">
        <div class="d-flex">
            <div class="name-location justify-content-end">
                <div class="first-line d-flex align-items-center">
                    <h3 class="display-name">
                        {{ currentEncounter.profile.displayName }}
                    </h3>
                    <span class="px-1">&middot;</span>
                    <span class="age">
                        {{ currentEncounter.profile.age }}
                    </span>
                    <i class="online-status flex-shrink-0 {{ currentEncounter.user.isOnline ? 'bg-green' : 'bg-gray' }}"
                       uib-tooltip="{{ currentEncounter.user.onlineTitle }}">
                    </i>
                </div>
                <div class="second-line d-flex flex-column align-content-center">
                    <div class="user-location">
                        {{ currentEncounter.profile.displayLocation }}
                    </div>
                    <div class="user-badges d-flex flex-row mt-2">
                        <div class="user-badge user-sex-badge sex-{{ currentEncounter.profile.sexAlias }} d-flex align-items-center justify-content-center mr-2"
                             uib-tooltip="{{ currentEncounter.profile.sexTitle }}">
                            <i class="{{ currentEncounter.profile.sexIcon }}"></i>
                        </div>
                        <div ng-show="currentEncounter.user.isVerified" class="user-badge user-verified-badge d-flex align-items-center justify-content-center mr-2" rel="tooltip"
                             title="<?= Yii::t('youdate', 'Verified user') ?>">
                            <i class="fe fe-check"></i>
                        </div>
                        <div ng-show="currentEncounter.user.isPremium" class="user-badge user-premium-badge d-flex align-items-center justify-content-center" rel="tooltip"
                             title="<?= Yii::t('youdate', 'Premium user') ?>">
                            <i class="fe fe-star"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="profile-info-block">
        <div class="row mb-0 mb-sm-4">
            <div class="col-12 col-sm-6 mb-4 mb-sm-0">
                <div class="text-bold mb-2"><?= Yii::t('app', 'Sex') ?>:</div>
                <div class="text-muted">
                    {{ currentEncounter.profile.sexTitle }}
                </div>
            </div>
            <div class="col-12 col-sm-6 mb-4 mb-sm-0">
                <div class="text-bold mb-2"><?= Yii::t('app', 'Status') ?>:</div>
                <div class="text-muted">
                    {{ currentEncounter.profile.statusTitle }}
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12 col-sm-6 mb-4 mb-sm-0">
                <div class="text-bold mb-2"><?= Yii::t('youdate', 'I am looking for') ?>:</div>
                <div class="text-muted">
                    {{ currentEncounter.profile.lookingForTitle }}
                </div>
            </div>
            <div class="col-12 col-sm-6">
                <div class="text-bold mb-2"><?= Yii::t('youdate', 'Aged') ?>:</div>
                <div class="text-muted">
                    {{ currentEncounter.profile.lookingForAgeTitle }}
                </div>
            </div>
        </div>
    </div>
    <div class="profile-info-block">
        <div class="text-bold mb-2"><?= Yii::t('youdate', 'Description') ?>:</div>
        <div class="text-muted">
            {{ currentEncounter.profile.description }}
        </div>
    </div>
</div>
