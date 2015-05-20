<div class="raw100 raw-left user-row">
    <div class="rg-row">
        <div class="rg-col-4">
            <p><a href="{{ URL::to('account/settings/subscription/download/'.Crypto::encrypt($invoice->id)) }}">
                {{ $invoice->dateString() }}
            </a></p>
        </div>
        <div class="rg-col-4">
            <p>{{ $invoice->id }}</p>
        </div>
        <div class="rg-col-4 text-right">
            <p class="raw-margin-right-16">{{ $invoice->dollars() }}</p>
        </div>
    </div>
</div>