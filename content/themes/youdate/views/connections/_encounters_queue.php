<div class="encounters-queue d-none d-md-flex flex-row flex-sm-column ml-auto ng-hide" ng-show="initialStateLoaded && hasEncounters()">
    <div class="queue-item" ng-repeat="queuedEncounter in encounters | limitTo:5" ng-if="$index < 5" ng-class="$index == 0 ? 'active' : ''">
        <img ng-src="{{ queuedEncounter.profile.avatar }}" alt="{{ queudEncounter.user.id }}">
    </div>
</div>
