$(document).ready(function() {

    var $btnStripe = $('.btn-stripe'),
        $buttons = $('.payment-add .btn'),
        $paymentAdd = $('.payment-add'),
        $loader = $('.payment-loader'),
        $form = $('.payment-form');

    function getAmount() {
        return $('.credits-input:checked').data('amount');
    }

    $btnStripe.on('click', function(event) {
        event.preventDefault();

        $buttons.addClass('disabled').attr('disabled', 'disabled');

        $form.attr('action', $form.data('action-stripe'));
        var data = $.extend({
            amount: getAmount() * 100,
            color: 'white'
        }, $btnStripe.data());
        var opts = $.extend(data, {
            token: function(result) {
                $form
                    .append($('<input>').attr({ type: 'hidden', name: 'stripeToken', value: result.id }))
                    .append($('<input>').attr({ type: 'hidden', name: 'amount', value: getAmount() }))
                    .submit();
                $paymentAdd.addClass('hidden');
                $loader.removeClass('hidden');
            },
            closed: function() {
                $buttons.removeClass('disabled').attr('disabled', false);
            }
        });

        StripeCheckout.open(opts);
    });

    $('.btn-paypal').on('click', function (event) {
        event.preventDefault();
        $form
            .attr('action', $form.data('action-paypal'))
            .append($('<input>').attr({ type: 'hidden', name: 'amount', value: getAmount() }))
            .submit();
        $paymentAdd.addClass('hidden');
        $loader.removeClass('hidden');
    });
    
});
