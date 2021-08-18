var app = angular.module('youdateEncounters', [
    'ui.bootstrap',
    'ngTouch'
]);

app.run(function run($http) {
    $http.defaults.headers.post['X-CSRF-Token'] = $('meta[name="csrf-token"]').attr("content");
});

app.controller('EncountersController', ['$scope', '$http', '$timeout', '$document', '$uibModal',
    function($scope, $http, $timeout, $document, $uibModal) {

    $scope.initialStateLoaded = false;
    $scope.encounters = [];
    $scope.currentEncounter = null;
    $scope.previousEncounter = null;

    $scope.onEncounterAction = function(action) {
        var userId = $scope.currentEncounter.user.id;
        $scope.popEncounter();

        return $http({ method: 'post', url: appBaseUrl() + 'connections/encounter-action', params: { action: action, toUserId: userId } })
            .then(function(response) {
                if (response.status === 200 && response.data.success) {
                    if (response.data.isMutual) {
                        $uibModal.open({
                            animation: true,
                            ariaLabelledBy: 'modal-title',
                            ariaDescribedBy: 'modal-body',
                            templateUrl: 'encounters-mutual.html',
                            size: 'md',
                            bindToController: true,
                            controller: 'EncountersMutualController',
                            controllerAs: '$ctrl',
                            scope: $scope
                        });
                    }
                }
            });
    };

    $scope.hasEncounters = function () {
        return $scope.encounters.length > 0 && $scope.initialStateLoaded;
    };

    $scope.getEncounters = function(popEncounter) {
        var params = {
            more: $scope.initialStateLoaded ? 0 : 1
        };
        if ($scope.encounters.length) {
            var ignoredIds = $scope.encounters.map(function(encounter) {
                return encounter.user.id;
            });
            ignoredIds.push($scope.currentEncounter.user.id);
            if ($scope.previousEncounter) {
                ignoredIds.push($scope.previousEncounter.user.id);
            }
            params.ignoredIds = ignoredIds.join(',');
        }
        return $http({ url: appBaseUrl() + 'connections/get-encounters', params: params })
            .then(function(response) {
                if (response.status === 200 && response.data.success) {
                    $scope.encounters = $scope.encounters.concat(response.data.encounters);
                    if ($scope.encounters.length && popEncounter) {
                        $scope.popEncounter();
                    }
                }
                if (!$scope.initialStateLoaded) {
                    $scope.initialStateLoaded = true;
                }
            });
    };

    $scope.popEncounter = function() {
        $scope.previousEncounter = $scope.currentEncounter;
        $scope.currentEncounter = $scope.encounters.shift();
        if ($scope.encounters.length <= 5) {
            $scope.getEncounters(false);
        }
    };

    $scope.getSexClass = function(value) {
        switch (value) {
            case 0:
                return 'user';
            case 1:
                return 'male';
            case 2:
                return 'female';
        }
    };

    $document.bind('keyup', function (event) {
        if (event.keyCode == 49) {
            return $scope.onEncounterAction('like');
        } else if (event.keyCode == 50) {
            return $scope.onEncounterAction('skip');
        }
    });

    $scope.getEncounters(true);
}]);

app.controller('EncountersMutualController', function ($uibModalInstance) {
    var $ctrl = this;
    $ctrl.close = function () {
        $uibModalInstance.close(false);
    };
});
