@extends('layouts.app')

@section('content')
<div class="container">
    @if (! $team->rounds()->find($round->id))
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <a href="{{ route('coach.rounds.index', $team) }}">&laquo; Rounds</a>
                <br />
                <div class="panel panel-default">
                    <div class="panel-heading">Add a Round {{ $round->name }} to {{ strtoupper($team->name) }}?</div>

                    <div class="panel-body">
                        <form action="{{ route('coach.round.store', [$team, $round]) }}" method="post">
                            {{ csrf_field() }}
                            <button type="submit" class="btn btn-primary">Yes, add this Round</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <a href="{{ route('coach.rounds.index', $team) }}">&laquo; Rounds</a>
                <br />
                <div class="panel panel-default">
                    <div class="panel-heading">Pick a date for Round {{ $round->name }}</div>

                    <div class="panel-body">
                        <form class="custom-horizontal" action="{{ route('coach.round.store.date', [$team, $round]) }}" method="post">
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
                        <coach-round-input team="{{ $team->slug }}" round="{{ $round->id }}"></coach-round-input>
                    </div>
                </div>
            </div>

            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">Remove this Round for {{ strtoupper($team->name) }}?</div>

                    <div class="panel-body">
                        <form action="{{ route('coach.round.destroy', [$team, $round]) }}" method="post">
                            {{ method_field('DELETE') }}
                            {{ csrf_field() }}
                            <button type="submit" class="btn btn-danger">Remove this Round</button>
                        </form>
                        <div class="help-block">
                            <p>Pressing the above button will delete all data available on this page.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
