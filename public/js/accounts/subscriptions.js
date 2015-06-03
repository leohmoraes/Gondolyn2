jQuery(function($) {
    $('#userSubscription').submit(function(event) {
        var $form = $(this);
        $form.find('button').prop('disabled', true);

        var _month = $("#expiry").val().substring(0, 2);
        var _year = $("#expiry").val().substring($("#expiry").val().indexOf("/")+2, $("#expiry").val().length);

        $('#exp_month').val(_month);
        $('#exp_year').val(_year);

        Stripe.card.createToken($form, stripeResponseHandler);

        return false;
    });

    /*
    |--------------------------------------------------------------------------
    | Confirm the subscription change
    |--------------------------------------------------------------------------
    */

    $('#changeBtn').click(function(e){
        e.preventDefault();

        $('#changeModal').modal('toggle');

        $('#confirmChangeBtn').bind('click', function(){
            $('#userSubscriptionChange').submit();
        });
    });
});

var stripeResponseHandler = function(status, response) {
    var $form = $('#userSubscription');

    if (response.error) {
        $form.find('.payment-errors').text(response.error.message);
        $form.find('button').prop('disabled', false);

        gondolynNotify(response.error.message);

    } else {
        var token = response.id;
        $form.append($('<input type="hidden" name="stripeToken" />').val(token));
        $form.get(0).submit();
    }
};

$('#userSubscription').card({ container: $('.card-wrapper') });
