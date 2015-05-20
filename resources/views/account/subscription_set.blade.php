@section('content')

<div class="raw100 raw-left text-center">
    <h2>Settings: Subscription</h2>
</div>

<div class="raw-device raw-margin-auto raw-margin-top-24">

    <div class="raw100 raw-left card-wrapper raw-margin-top-24"></div>

    <div class="raw100 raw-left raw-margin-top-24">
        <form id="userSubscription" method="post" action="{{ URL::to('account/settings/set/subscription') }}">
            <?= Form::token(); ?>
            <input id="exp_month" type="hidden" name="exp_month" data-stripe="exp-month" />
            <input id="exp_year" type="hidden" name="exp_year" data-stripe="exp-year"/>
            <div class="raw100 raw-left rg-row raw-margin-top-24">
                <div class="rg-col-4">
                    <label for="number" class="raw-margin-top-8 raw-right">Card Number</label>
                </div>
                <div class="rg-col-8">
                    <input id="number" type="text" name="number" class="form-control" placeholder="Card Number" data-stripe="number">
                </div>
            </div>
            <div class="raw100 raw-left rg-row raw-margin-top-24">
                <div class="rg-col-4">
                    <label for="name" class="raw-margin-top-8 raw-right">Full Name</label>
                </div>
                <div class="rg-col-8">
                    <input id="name" type="text" name="name" class="form-control" placeholder="Full Name" data-stripe="name">
                </div>
            </div>
            <div class="raw100 raw-left rg-row raw-margin-top-24">
                <div class="rg-col-4">
                    <label for="cvc" class="raw-margin-top-8 raw-right">CVV / Expiry</label>
                </div>
                <div class="rg-col-4">
                    <input id="cvc" type="text" name="cvc" class="form-control" placeholder="CVC" data-stripe="cvc">
                </div>
                <div class="rg-col-4">
                    <input id="expiry" type="text" name="expiry" class="form-control" placeholder="MM/YY">
                </div>
            </div>
            <div class="raw100 raw-left rg-row raw-margin-top-24">
                <div class="rg-col-4">
                    <label for="cvv" class="raw-margin-top-8 raw-right">Plan</label>
                </div>
                <div class="rg-col-8">
                    @yield('selectPlan')
                </div>
            </div>
            <div class="raw100 raw-left rg-row raw-margin-top-24">
                <input id="update" type="submit" class="btn btn-primary raw-right" value="Subscribe">
            </div>
        </form>
    </div>
</div>

<script type="text/javascript" src="https://js.stripe.com/v2/"></script>
<script type="text/javascript">

    Stripe.setPublishableKey('<?= Config::get("gondolyn.stripe.publish_key"); ?>');

</script>

@stop

@section('page_js')

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

@stop