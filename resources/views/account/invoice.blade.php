<table class="table table-striped">

    <thead>
        <th>Date</th>
        <th>Identifier</th>
        <th>Dollars</th>
    </thead>

    <tbody>

        @foreach($invoices as $invoice)
        <tr>
            <td><a href="{{ URL::to('account/settings/subscription/download/'.Crypto::encrypt($invoice->id)) }}">{{ $invoice->dateString() }}</a></td>
            <td>{{ $invoice->id }}</td>
            <td>{{ $invoice->dollars() }}</td>
        </tr>
        @endforeach

    </tbody>

</table>