@section('selectRole')

<select name="role" class="form-control">
    @foreach($options as $option)

        <option value="{{ $option }}" {!! ($option === $user->user_role) ? "selected" : "" !!}>{{ $option }}</option>

    @endforeach
</select>

@stop