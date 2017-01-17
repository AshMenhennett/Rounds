@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Pick a date for Round {{ $round->name }}</div>

                <div class="panel-body">
                    <form class="custom-horizontal" action="{{ route('round.store.date', [$team, $round]) }}" method="post">
                        <div class="col-md-8 form-group{{ $errors->has('date') ? ' has-error' : ''}}">
                            <span><strong>Current Set Date</strong>: {{ Carbon\Carbon::createFromTimeStamp(strtotime($round->date($team)))->format('d/m/Y') }}.</span>
                            <input type="date" name="date" class="form-control" value="{{ old('date') ? old('date') : '' }}" pattern="[0-9]{2}/[0-9]{2}/[0-9]{4}" required />
                            @if ($errors->has('date'))
                                <div class="help-block danger">
                                    Please enter a date in the format: DD/MM/YYYY.
                                </div>
                            @endif
                        </div>

                        <div class="col-md-4 form-group">
                            {{ csrf_field() }}
                            <button type="submit" class="btn btn-primary pull-right">Set Date</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Fill in Round {{ $round->name }}</div>

                <div class="panel-body">
                    <round-input team="{{ $team->slug }}" round="{{ $round->id }}"></round-input>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
