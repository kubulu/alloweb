
var translateHelpers = (function () {

    /**
     * @type Boolean
     */
    var _load = false;

    /**
     * Remove alert box.
     */
    function _hideMessages() {
        setTimeout(function () {
            $('.alert-box').remove();
        }, 5000);
    }

    /**
     * Remove alert tooltip.
     */
    function _hideTooltip() {
        setTimeout(function () {
            $('#alert-tooltip').remove();
        }, 500);
    }

    function _createMessage(message, type) {
        return $('<div>')
            .attr({'class': 'alert-box', role: 'alert'})
            .addClass('alert')
            .addClass(typeof (type) === 'undefined' ? 'alert-info' : type)
            .text(message);
    }

    return {
        /**
         * @param {string} url
         * @param {json} $data
         */
        post: function (url, $data) {
            if (_load === false) {
                _load = true;
                $.post(url, $data, $.proxy(function (data) {
                    _load = false;
                    this.showTooltip(data);
                }, this), 'json');
            }
        },
        /**
         * Show alert tooltip.
         * @param {json} $data
         */
        showTooltip: function ($data) {

            if ($('#alert-tooltip').length === 0) {
                var $alert = $('<div>')
                    .attr({id: 'alert-tooltip'})
                    .addClass($data.length === 0 ? 'green' : 'red')
                    .append($('<span>')
                        .addClass('glyphicon')
                        .addClass($data.length === 0 ? ' glyphicon-ok' : 'glyphicon-remove'));

                $('body').append($alert);
                _hideTooltip();
            }
        },
        /**
         * Show messages.
         * @param {json} $data
         * @param {string} container
         */
        showMessages: function ($data, container) {

            if ($('.alert-box').length) {
                $('.alert-box').append($data);
            } else {
                $(typeof (container) === 'undefined' ? $('body').find('.container').eq(1) : container).prepend(_createMessage($data));
                _hideMessages();
            }
        },
        /**
         * Show error messages.
         * @param {json} $data
         * @param {string} prefix
         */
        showErrorMessages: function ($data, prefix) {
            for (i in $data) {
                var k = 0;
                $messages = new Array();
                if (typeof ($data[i]) === 'object') {
                    for (j in $data[i]) {
                        $messages[k++] = $data[i][j];
                    }
                } else {
                    $messages[k++] = $data[i];
                }

                this.showErrorMessage($messages.join(' '), prefix + i);
            }
            _hideMessages();
        },
        /**
         * Show error message.
         * @param {string} message
         * @param {string} id
         */
        showErrorMessage: function (message, id) {
            $(id).next().html(_createMessage(message, 'alert-danger'));
        }
    };
})();


var translate = (function () {

    var _originalMessage;

    function _translateLanguage($this) {
        var $translation = $this.closest('tr').find('.translation');

        var data = {
            id: $translation.data('id'),
            language_id: $('#language_id').val(),
            translation: $.trim($translation.val())
        };

        translateHelpers.post($('#language_id').data('url'), data);
    }

    function _copySourceToTranslation($this) {
        var $translation = $this.closest('tr').find('.translation'),
            isEmptyTranslation = $.trim($translation.val()).length === 0,
            sourceMessage = $.trim($this.val());

        if (!isEmptyTranslation) {
            return;
        }

        $translation.val(sourceMessage);
        _translateLanguage($this);
    }

    return {
        init: function () {
            var $body = $('body');

            $body.on('click', '#translates .source', function () {
                _copySourceToTranslation($(this));
            });
            $body.on('click', '#translates button', function () {
                _translateLanguage($(this));
            });
            $body.on('focus', '#translates .translation', function () {
                _originalMessage = $.trim($(this).val());
            });
            $body.on('blur', '#translates .translation', function () {
                if ($.trim($(this).val()) !== _originalMessage) {
                    _translateLanguage($(this).closest('tr').find('button'));
                }
            });
            $body.on('change', "#translates #search-form select", function(){
                $(this).parents("form").submit();
            });
        }
    };
})();

var Language = {

    init: function() {
        $('#languages').on('change', 'select.status', $.proxy(function(event) {
            this.changeStatus($(event.currentTarget));
        }, this));
    },

    changeStatus: function($object) {
        var $data = {
            language_id: $object.attr('id'),
            status: $object.val()
        };
        translateHelpers.post($object.data('url'), $data);
    }
};

var scan = {
    object: null,
    checked: false,
    init: function () {
        $('body').on('click', 'button#select-all', $.proxy(function () {
            this.toggleChecked();
        }, this));
        $('body').on('click', 'button#delete-selected', $.proxy(function () {
            if (window.confirm('Are you sure you want to delete these items?')) {
                this.deleteSelected();
            }
        }, this));
        $('body').on('click', 'a.delete-item', $.proxy(function (event) {
            if (window.confirm('Are you sure you want to delete this item?')) {
                this.deleteItem($(event.currentTarget));
            }
            return false;
        }, this));
    },
    toggleChecked: function () {
        this.checked = !this.checked;
        $('#delete-source').find('input.language-source-cb').prop("checked", this.checked);
    },
    deleteSelected: function () {
        var $ids = new Array();

        this.object = $('#delete-source').find('input.language-source-cb:checked');
        this.object.each(function () {
            $ids.push($(this).val());
        });

        this.delete($ids);
    },
    deleteItem: function ($object) {
        this.object = $object;

        var $ids = new Array();
        $ids.push(this.object.data('id'));

        this.delete($ids);
    },
    delete: function ($ids) {
        if ($ids.length) {
            $.post($('#delete-source').find('a').attr('href'), {ids: $ids}, $.proxy(function () {
                this.remove();
            }, this));
        }
    },
    remove: function () {
        this.object.closest('tr').remove();

        var text = $('#w2-danger').text();
        var pattern = /\d+/g;
        var number = pattern.exec(text);
        $('#w2-danger').text(text.replace(number, $('#delete-source').find('tbody').find('tr').length));

        if ($('#delete-source').find('tbody').find('tr').length === 0) {
            $('#delete-source, #select-all, #delete-selected').remove();
        }
    }
};

$(document).ready(function () {
    translate.init();
    Language.init();
    scan.init();
});
