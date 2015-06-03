@extends('layouts.standard')

@section('content')

<!-- Content -->

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
                'invoiceTab' => true
            ])

        </div>
        <div class="tab-content">
            <div role="tabpanel" class="tab-pane active" id="invoices">
                <div class="raw100 raw-left raw-margin-top-24">

                    {!! $invoices !!}

                </div>
            </div>
        </div>
    </div>
</div>

@stop