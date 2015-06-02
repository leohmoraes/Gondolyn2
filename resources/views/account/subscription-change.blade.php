@extends('layouts.standard')

@section('content')

<!-- Modal -->
<div class="modal fade" id="cancelModal" tabindex="-1" role="dialog" aria-labelledby="cancelModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel">Cancel My Subscription</h4>
            </div>
            <div class="modal-body">
                <p>Are you sure you wish to cancel your subscription?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <a id="cancelBtn" type="button" class="btn btn-danger" href="{{ URL::to('account/cancel/subscription') }}">Confirm Cancelation</a>
            </div>
        </div>
    </div>
</div>

<!-- Content -->

<div class="raw100 rg-row raw-margin-top-24">
    <div class="rg-col-4 text-center raw-block-350">
        <div class="raw100 raw-left raw-margin-top-48">
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
                <div class="raw100 raw-left raw-margin-top-24">
                    <button class="raw-right btn btn-danger" data-toggle="modal" data-target="#cancelModal">Cancel Subscription</button>

                    <form id="userSubscription" method="post" action="{{ URL::to('account/settings/update/subscription') }}">
                        <?= Form::token(); ?>
                        <div class="raw100 raw-left rg-row raw-margin-top-24">
                            <label for="plan">Plan</label>
                            @include('account.select-plan')
                        </div>
                        <div class="raw100 raw-left rg-row raw-margin-top-24">
                            <input id="update" type="submit" class="btn btn-primary raw-right" value="Save Change">
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript" src="https://js.stripe.com/v2/"></script>
<script type="text/javascript">

    Stripe.setPublishableKey('<?= Config::get("gondolyn.stripe.publish_key"); ?>');

</script>

@stop