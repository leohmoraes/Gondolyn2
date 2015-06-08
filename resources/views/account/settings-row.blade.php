@if(stristr($input, 'checkbox'))
<div class="raw100 raw-left raw-margin-top-24">
    <div class="form-group {{ $errorHighlight }}">
        <label class="{{ $errorHighlight }}" for="{!! $labelFor !!}">{!! $input !!}  {!! $label !!}</label>
    </div>
    {!! $errorMessage !!}
</div>
@else
<div class="raw100 raw-left raw-margin-top-24">
    <div class="form-group {{ $errorHighlight }}">
        <label class="control-label" for="{!! $labelFor !!}">{!! $label !!}</label>
        <div class="raw100 raw-left">
            {!! $input !!}
        </div>
    </div>
    {!! $errorMessage !!}
</div>
@endif