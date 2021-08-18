var app = {
    _baseUrl: null,
    init: function() {
        this._baseUrl =  $('meta[name=baseUrl]').attr("content");
    },
    getUrl: function(url) {
        return this._baseUrl + url;
    },
    t: function(category, message) {
        return message;
    }
};

$(document).ready(function() {

    var $body = $('body'),
        $window = $(window),
        $document = $(document);

    app.init();

    /**
     * Tooltip plugin
     */
    $body.tooltip({
        selector: '[rel=tooltip]'
    });

    /**
     * Fancybox
     */
    $('[data-fancybox]').fancybox({
        toolbar: false,
        clickContent: 'close',
        clickSlide: 'close'
    });

    /**
     * Messenger plugin
     */
    Messenger.options = {
        extraClasses: 'messenger-fixed messenger-on-bottom messenger-on-left',
        theme: 'flat'
    };
    $(document).ajaxError(function(event, jqXHR, ajaxSettings, thrownError) {
        var data = eval("(" + jqXHR.responseText + ")");
        Messenger().post({
            message: data.message,
            type: 'error',
            hideAfter: 5,
            hideOnNavigate: true
        });
    });


    $body.on('click', '.list-view .btn-action', function(event) {
        var $btn = $(this), $listItem = $btn.closest('.list-item');
        $listItem.find('.btn').attr('disabled', 'disabled');
        $.ajax({
            url: $btn.attr('data-url'),
            method: 'post',
            success: function(data) {
                if (data.success) {
                    Messenger().post({
                        message: data.message,
                        type: 'success',
                        hideAfter: 5,
                        hideOnNavigate: true
                    });
                    $.pjax.reload({ container: '#pjax-list-view' });
                } else {
                    Messenger().post({
                        message: data.message,
                        type: 'error',
                        hideAfter: 5,
                        hideOnNavigate: true
                    });
                }
            },
            complete: function() {
                $listItem.find('.btn').attr('disabled', false);
            }
        });
    });
});
