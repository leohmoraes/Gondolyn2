<table class="table table-striped">

    <thead>
        <th>Date</th>
        <th class="raw-m-hide raw-t-hide">Identifier</th>
        <th class="raw-m-hide">Dollars</th>
    </thead>

    <tbody>

        @foreach($invoices as $invoice)
        <tr>
            <td><a href="{{ URL::to('account/settings/subscription/download/'.Crypto::encrypt($invoice->id)) }}">{{ $invoice->dateString() }}</a></td>
            <td class="raw-m-hide raw-t-hide">{{ $invoice->id }}</td>
            <td class="raw-m-hide">{{ $invoice->dollars() }}</td>
        </tr>
        @endforeach

    </tbody>

</table>