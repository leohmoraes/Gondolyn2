@section('selectPlan')

<select name="plan" class="form-control">
    @foreach($packages as $option)

        <option value="{{ $option['id'] }}" {{ ($option['stripe_id'] === $user->stripe_plan) ? "selected" : "" }}>{{ $option['name'] }}</option>

    @endforeach
</select>

@stop