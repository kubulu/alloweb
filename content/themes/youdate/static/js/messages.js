var app = angular.module('youdateMessages', [
    'luegg.directives',
    'ngBootbox',
    'ui.bootstrap',
    'angularLazyImg'
]);

app.run(function run($http) {
    $http.defaults.headers.post['Content-Type'] = 'application/x-www-form-urlencoded;charset=utf-8';
    $http.defaults.headers.post['X-CSRF-Token'] = $('meta[name="csrf-token"]').attr("content");

    /**
     * @param {Object} obj
     * @return {String}
     */
    var param = function(obj) {
        var query = '', name, value, fullSubName, subName, subValue, innerObj, i;

        for(name in obj) {
            value = obj[name];

            if(value instanceof Array) {
                for(i=0; i<value.length; ++i) {
                    subValue = value[i];
                    fullSubName = name + '[' + i + ']';
                    innerObj = {};
                    innerObj[fullSubName] = subValue;
                    query += param(innerObj) + '&';
                }
            }
            else if(value instanceof Object) {
                for(subName in value) {
                    subValue = value[subName];
                    fullSubName = name + '[' + subName + ']';
                    innerObj = {};
                    innerObj[fullSubName] = subValue;
                    query += param(innerObj) + '&';
                }
            }
            else if(value !== undefined && value !== null)
                query += encodeURIComponent(name) + '=' + encodeURIComponent(value) + '&';
        }

        return query.length ? query.substr(0, query.length - 1) : query;
    };

    $http.defaults.transformRequest = [function(data) {
        return angular.isObject(data) && String(data) !== '[object File]' ? param(data) : data;
    }];
});

app.directive('myEnter', function () {
    return function (scope, element, attrs) {
        element.bind("keydown keypress", function (event) {
            if (event.which === 13) {

                scope.$apply(function (){
                    scope.$eval(attrs.myEnter);
                });
                event.preventDefault();
            }
        });
    };
});

app.directive('a', function() {
    return {
        restrict: 'E',
        link: function(scope, elem, attrs) {
            if (attrs.ngClick || attrs.href === '' || attrs.href === '#') {
                elem.on('click', function(e){
                    e.preventDefault();
                });
            }
        }
    };
});

app.controller('MessagesController', ['$scope', '$http', function($scope, $http) {

    var pendingMessageId = 0;
    var updateIntervalId = null;

    $scope.selectedConversationIdx = null;
    $scope.currentContactId = null;
    $scope.currentContact = null;
    $scope.currentContactBanned = false;
    $scope.currentDate = null;
    $scope.loading = false;
    $scope.initialStateLoaded = false;
    $scope.message = null;
    $scope.conversationsQuery = null;

    $scope.conversations = {};
    $scope.messages = {};
    $scope.selectedMessages = [];
    $scope.loggedUser = {
        id: $('.card-messages').attr('data-user-id'),
        avatar: $('.card-messages').attr('data-user-avatar'),
        fullName: $('.card-messages').attr('data-user-fullname')
    };

    $scope.reportReason = null;
    $scope.reportDescription = null;

    $scope.setCurrentContactId = function(id, key) {
        $scope.toggleConversations();
        if ($scope.currentContactId === id) {
            return;
        }
        $scope.message = '';
        $scope.currentContactId = id;
        $scope.selectedConversationIdx = key;
        $scope.selectedMessages = [];
        $scope.getMessages();
        $scope.resetReportForm();
        setTimeout($scope.readCurrentConversation, 1000);
    };

    $scope.getConversations = function() {
        return $http({ url: appBaseUrl() + 'messages/conversations', params: { query: $scope.conversationsQuery } })
            .then(function(response) {
                if (response.status === 200 && response.data.success) {
                    $scope.conversations = response.data.conversations;
                    $scope.newMessagesCounts = response.data.newMessagesCounts;
                }
                if (!$scope.initialStateLoaded) {
                    $scope.initialStateLoaded = true;
                    $scope.resetCurrentContact();
                }
            });
    };

    $scope.resetCurrentContact = function() {
        if (Object.keys($scope.conversations).length) {
            var firstConversation = first($scope.conversations);
            $scope.setCurrentContactId(firstConversation.contact.id);
            $scope.currentContact = firstConversation.contact;
        }
    };

    $scope.toggleConversations = function() {
        var $sidebar = $('.col-messages-conversations'),
            $chat = $('.col-messages-conversation');

        if ($sidebar.hasClass('active')) {
            $sidebar.removeClass('active');
            $chat.removeClass('hide');
        } else {
            $sidebar.addClass('active');
            $chat.addClass('hide');
        }
    };

    $scope.readCurrentConversation = function () {
        if ($scope.currentContactBanned === true) {
            return;
        }
        $http.post(appBaseUrl() + 'messages/read-conversation', { contactId: $scope.currentContactId })
            .then(function() {
                setTimeout(function() {
                    for (var k in $scope.messages) {
                        if ($scope.messages.hasOwnProperty(k)) {
                            if ($scope.messages[k].is_new && $scope.messages[k].to_user_id == $scope.loggedUser.id) {
                                $scope.messages[k].is_new = false;
                            }
                        }
                        delete $scope.newMessagesCounts[$scope.currentContactId];
                    }
                }, 1000);
            });
    };

    $scope.hasContacts = function () {
        return Object.keys($scope.conversations).length > 0 && $scope.initialStateLoaded;
    };

    $scope.sendMessage = function() {

        if (!$scope.message.length) {
            return;
        }

        $scope.messages['pending_' + pendingMessageId] = {
            id: 'pending_' + pendingMessageId,
            pending: true,
            type: 'sent',
            text: $scope.message,
            datetime: new Date(),
            is_new: true,
            user: {
                avatar: $scope.loggedUser.avatar,
                full_name: $scope.loggedUser.fullName
            }
        };

        $http.post(appBaseUrl() + 'messages/create', {
            contactId: $scope.currentContactId,
            message: $scope.message,
            pendingMessageId: pendingMessageId
        }).then(function(response) {
            if (response.status === 200 && response.data.success) {
                $scope.messages[response.data.messageId] = $scope.messages['pending_' + response.data.pendingMessageId];
                $scope.messages[response.data.messageId].id = response.data.messageId;
                $scope.messages[response.data.messageId].pending = false;
                $scope.getMessages();
            } else {
                Messenger().post({ message: response.data.message, type: 'error' });
            }
            delete $scope.messages['pending_' + response.data.pendingMessageId];
        });

        pendingMessageId++;
        $scope.message = '';
    };

    $scope.addEmoji = function(emoji) {
        if ($scope.message) {
            $scope.message += emoji;
        } else {
            $scope.message = emoji;
        }
        $('.message-input').focus();
    };

    $scope.getMessages = function() {
        $http.get(appBaseUrl() + 'messages/messages', {
            params: {
                contactId: $scope.currentContactId
            }
        }).then(function(response) {
            if (response.status === 200 && response.data.success) {
                var data = response.data;
                $scope.currentContactBanned = false;
                $scope.messages = data.messages;
                $scope.currentContact = {
                    id: data.contact.id,
                    avatar: data.contact.avatar,
                    fullName: data.contact.full_name,
                    url: data.contact.url,
                    online: data.contact.online,
                    verified: data.contact.verified
                }
            }
        }, function(errorResponse) {
            if (errorResponse.status === 404) {
                $scope.currentContactBanned = true;
            } else {
                Messenger().post('Unknown error occurred');
            }
        });
    };

    $scope.sendReport = function() {
        $http.post(appBaseUrl() + 'report/create', {
            reportedUserId: $scope.currentContact.id,
            reason: $scope.reportReason,
            description: $scope.reportDescription
        }).then(function(response) {
            if (response.status === 200 && response.data.success) {
                Messenger().post(response.data.message);
                $scope.resetReportForm();
            }
            $('#conversation-report').modal('toggle');
        });
    };

    $scope.getTime = function(dateTime) {
        if (isSafari || isExplorer) {
            if (dateTime && typeof dateTime === 'string') {
                dateTime = dateTime.replace(/-/g, "/");
            }
        }
        var dt = new Date(dateTime);
        return dt.getHours() + ':' + (dt.getMinutes() < 10 ? '0' : '') + dt.getMinutes();
    };

    $scope.getCurrentDate = function() {
        return $scope.currentDate.toLocaleDateString();
    };

    $scope.checkMessageDatetime = function(dateTime) {
        if (isSafari || isExplorer) {
            if (dateTime && typeof dateTime === 'string') {
                dateTime = dateTime.replace(/-/g, "/");
            }
        }
        var dt = new Date(dateTime);
        if ($scope.currentDate === null) {
            $scope.currentDate = dt;
            return true;
        } else {
            if (dt.getDate() !== $scope.currentDate.getDate()) {
                $scope.currentDate = dt;
                return true;
            }
        }
        return false;
    };

    $scope.toggleMessage = function(id) {
        if ($scope.isMessageSelected(id)) {
            $scope.selectedMessages.splice($scope.selectedMessages.indexOf(id));
        } else {
            $scope.selectedMessages.push(id);
        }
    };

    $scope.isMessageSelected = function (id) {
        return $scope.selectedMessages.indexOf(id) > -1;
    };

    $scope.getItemClasses = function(item) {
        var cssClass = '';
        cssClass += $scope.isMessageSelected(item.id) ? ' selected' : '';
        cssClass += item.is_new ? ' new' : ' read';
        return cssClass;
    };

    $scope.isMessagePending = function(item) {
        if (typeof item.pending === 'undefined') {
            return false;
        }
        return item.pending;
    };

    $scope.onMessageHover = function(item) {
        if (item.is_new && item.to_user_id == $scope.loggedUser.id) {
            $scope.readCurrentConversation();
        }
    };

    $scope.deleteSelectedMessages = function() {
        var $btn = $('.delete-selected-messages');
        bootbox.confirm({
            message: $btn.attr('data-confirm-title'),
            title: $btn.attr('data-title'),
            buttons: {
                confirm: {
                    label: $btn.attr('data-confirm-button'),
                    className: 'btn-danger'
                },
                cancel: {
                    label: $btn.attr('data-cancel-button'),
                    className: 'btn-secondary'
                }
            },
            callback: function (result) {
                if (!result) {
                    return;
                }
                $http.post(appBaseUrl() + 'messages/delete', {
                    messages: $scope.selectedMessages
                }).then(function(response) {
                    if (response.status === 200 && response.data.success) {
                        Messenger().post(response.data.message);
                        $scope.selectedMessages.forEach(function(item, i, arr) {
                            delete $scope.messages[item];
                        });
                        $scope.selectedMessages = [];
                    }
                });
            }
        });
    };

    $scope.deleteConversation = function() {
        var $btn = $('.delete-conversation');
        bootbox.confirm({
            message: $btn.attr('data-confirm-title'),
            title: $btn.attr('data-title'),
            buttons: {
                confirm: {
                    label: $btn.attr('data-confirm-button'),
                    className: 'btn-danger'
                },
                cancel: {
                    label: $btn.attr('data-cancel-button'),
                    className: 'btn-secondary'
                }
            },
            callback: function (result) {
                if (!result) {
                    return;
                }
                $http.post(appBaseUrl() + 'messages/delete-conversation', {
                    contactId: $scope.currentContactId
                }).then(function(response) {
                    if (response.status === 200 && response.data.success) {
                        Messenger().post(response.data.message);
                        $scope.getConversations();
                    }
                });
            }
        });
    };

    $scope.resetReportForm = function () {
        $scope.reportReason = null;
        $scope.reportDescription = null;
    };

    $scope.bootboxDeleteButtons = {
        cancel: {
            className: "btn-secondary"
        },
        danger: {
            className: "btn-danger"
        }
    };

    var refresh = function () {
        $scope.getConversations().then(function () {
            if ($scope.currentContactId) {
                $scope.getMessages();
            }
        });
        console.log('update - ' + new Date());
    };

    var startUpdating = function() {
        updateIntervalId = setInterval(refresh, 3500);
    };

    var stopUpdating = function() {
        clearInterval(updateIntervalId);
        updateIntervalId = null;
    };

    var resetUpdatingTimer = function() {
        stopUpdating();
        startUpdating();
    };

    var init = function() {
        bootbox.setDefaults({
            show: true,
            backdrop: true,
            closeButton: false,
            animate: true,
            buttons: {
                cancel: {
                    className: 'btn btn-secondary'
                }
            }
        });
        $(window).blur(stopUpdating);
        $(window).mousemove(function(event) {
            if (updateIntervalId === null) {
                startUpdating();
            }
        });
        startUpdating();
        refresh();
    };

    init();
}]);
