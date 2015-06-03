@extends('layouts.standard')

@section('content')

    <div class="raw100 rg-row raw-margin-top-24">
        <div class="rg-col-4 text-center">
            <div class="raw100 raw-left raw-margin-top-24 raw-margin-bottom-48">
                <div class="gondolyn-profile-container">
                    <div class="gondolyn-profile" style="background-image: url({{ $profileImage }})" ></div>
                </div>
            </div>
        </div>
        <div class="rg-col-8">
            <div class="tab-panel">

                @include('account.tab-menu', [
                    'subscriptionTab' => true
                ])

            </div>
            <div class="tab-content">
                <div role="tabpanel" class="tab-pane active" id="subscription">

                    <div class="raw100 raw-left card-wrapper raw-margin-top-48 raw-margin-bottom-24 raw-block-200"></div>

                    <form id="userSubscription" method="post" action="{{ URL::to('account/settings/set/subscription') }}">
                        <?= Form::token(); ?>
                        <input id="exp_month" type="hidden" name="exp_month" data-stripe="exp-month" />
                        <input id="exp_year" type="hidden" name="exp_year" data-stripe="exp-year"/>
                        <div class="raw100 raw-left rg-row raw-margin-top-24">
                            <label for="number">Card Number</label>
                            <input id="number" type="text" name="number" class="form-control" placeholder="Card Number" data-stripe="number">
                        </div>
                        <div class="raw100 raw-left rg-row raw-margin-top-24">
                            <label for="name">Full Name</label>
                            <input id="name" type="text" name="name" class="form-control" placeholder="Full Name" data-stripe="name">
                        </div>
                        <div class="raw100 raw-left rg-row">
                            <div class="raw100 raw-left">
                                <div class="rg-inner-6 raw-margin-top-24">
                                    <label for="cvc">CVV</label>
                                    <input id="cvc" type="text" name="cvc" class="form-control" placeholder="CVV" data-stripe="cvc">
                                </div>
                                <div class="rg-inner-6 raw-margin-top-24">
                                    <label for="cvc">Expiry</label>
                                    <input id="expiry" type="text" name="expiry" class="form-control" placeholder="MM/YY">
                                </div>
                            </div>
                        </div>
                        <div class="raw100 raw-left rg-row raw-margin-top-24">
                            <label for="plan">Plan</label>
                             @include('account.select-plan')
                        </div>
                        <div class="raw100 raw-left rg-row raw-margin-top-24">
                            <input id="update" type="submit" class="btn btn-primary raw-right" value="Subscribe">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>


<script type="text/javascript" src="https://js.stripe.com/v2/"></script>
<script type="text/javascript">

    Stripe.setPublishableKey('<?= Config::get("gondolyn.stripe.publish_key"); ?>');

</script>

@stop

@section('javascript')

    {!! Minify::javascript(url('/js/accounts/subscriptions.js')) !!}

@stop