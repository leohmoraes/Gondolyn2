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
                <a id="cancelBtn" type="button" class="btn btn-danger" href="{{ URL::to('user/cancel/subscription') }}">Confirm Cancelation</a>
            </div>
        </div>
    </div>
</div>

<!-- Content -->

<div class="raw100 raw-left text-center">
    <h2>Settings: Change Subscription</h2>
</div>

<div class="raw-device raw-margin-auto raw-margin-top-24">

    <div class="raw100 raw-left raw-margin-top-24">
        <button class="raw-right btn btn-danger raw-margin-left-24" data-toggle="modal" data-target="#cancelModal">Cancel Subscription</button>

        <form id="userSubscription" method="post" action="{{ URL::to('user/settings/update/subscription') }}">
            <?= Form::token(); ?>
            <div class="raw100 raw-left rg-row raw-margin-top-24">
                <div class="rg-col-4">
                    <label for="cvv" class="raw-margin-top-8 raw-right">Plan</label>
                </div>
                <div class="rg-col-8">
                    @yield('selectPlan')
                </div>
            </div>
            <div class="raw100 raw-left rg-row raw-margin-top-24">
                <input id="update" type="submit" class="btn btn-primary raw-right" value="Save Change">
            </div>
        </form>
    </div>
</div>

<script type="text/javascript" src="https://js.stripe.com/v2/"></script>
<script type="text/javascript">

    Stripe.setPublishableKey('<?= Config::get("gondolyn.stripe.publish_key"); ?>');

</script>

@stop