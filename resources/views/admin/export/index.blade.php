@extends('layouts.app')

@section('content')
<div class="container">
    @if (! $teams->count())
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <a href="{{ route('admin.home') }}">&laquo; Dashboard</a>
                <br />
                <div class="panel panel-default">
                    <div class="panel-heading">Export Data</div>

                    <div class="panel-body">
                        <div class="alert alert-info text-center">
                            <p><strong>Uh Oh!</strong> There are no teams available to export data for.</p>
                            <p>Go back to the <a class="alert-link" href="{{ route('admin.home') }}">Admin Dashboard</a>?</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <a href="{{ route('admin.home') }}">&laquo; Dashboard</a>
                <br />
                <div class="panel panel-default">
                    <div class="panel-heading">Export Team Best Players</div>

                    <div class="panel-body">
                        <form action="{{ route('admin.export.bestPlayers') }}" method="post">
                            <div class="col-md-8 form-group{{ $errors->has('teamForBestPlayers') ? ' has-error' : ''}}">

                                <select class="form-control" name="teamForBestPlayers" required>
                                    @foreach ($teams as $team)
                                        <option value="{{ $team->id }}">{{ $team->name }}</option>
                                    @endforeach
                                </select>
                                @if ($errors->has('teamForBestPlayers'))
                                    <div class="help-block danger">
                                        {{ $errors->first('teamForBestPlayers') }}
                                    </div>
                                @endif
                            </div>

                            <div class="col-md-4 form-group">
                                {{ csrf_field() }}
                                <button type="submit" class="btn btn-primary pull-right">Export Team Best Players</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">Export All Team Data</div>

                    <div class="panel-body">
                        <form action="{{ route('admin.export.allByTeam') }}" method="post">
                            <div class="col-md-8 form-group{{ $errors->has('teamForTeamData') ? ' has-error' : ''}}">

                                <select class="form-control" name="teamForTeamData" required>
                                    @foreach ($teams as $team)
                                        <option value="{{ $team->id }}">{{ $team->name }}</option>
                                    @endforeach
                                </select>
                                @if ($errors->has('teamForTeamData'))
                                    <div class="help-block danger">
                                        {{ $errors->first('teamForTeamData') }}
                                    </div>
                                @endif
                            </div>

                            <div class="col-md-4 form-group">
                                {{ csrf_field() }}
                                <button type="submit" class="btn btn-primary pull-right">Export All Team Data</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">Export Player Quarter Data</div>

                    <div class="panel-body">
                        <div class="panel-body">
                            <form action="{{ route('admin.export.playerQuarters') }}" method="post">
                                <div class="col-md-8 form-group{{ $errors->has('playerForQuarterData') ? ' has-error' : ''}}">

                                    <select class="form-control" name="playerForQuarterData" required>
                                        @foreach ($teams as $team)
                                            @foreach ($team->players as $player)
                                                <option value="{{ $player->id }}">{{ $player->name }} of {{ $team->name }}</option>
                                            @endforeach
                                        @endforeach
                                    </select>
                                    @if ($errors->has('playerForQuarterData'))
                                        <div class="help-block danger">
                                            {{ $errors->first('playerForQuarterData') }}
                                        </div>
                                    @endif
                                </div>

                                <div class="col-md-4 form-group">
                                    {{ csrf_field() }}
                                    <button type="submit" class="btn btn-primary pull-right">Export Player Quarter Data</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">Export All Team Quarter Data</div>

                    <div class="panel-body">
                        <form action="{{ route('admin.export.teamQuarters') }}" method="post">
                            <div class="col-md-8 form-group{{ $errors->has('teamForQuarterData') ? ' has-error' : ''}}">

                                <select class="form-control" name="teamForQuarterData" required>
                                    @foreach ($teams as $team)
                                        <option value="{{ $team->id }}">{{ $team->name }}</option>
                                    @endforeach
                                </select>
                                @if ($errors->has('teamForQuarterData'))
                                    <div class="help-block danger">
                                        {{ $errors->first('teamForQuarterData') }}
                                    </div>
                                @endif
                            </div>

                            <div class="col-md-4 form-group">
                                {{ csrf_field() }}
                                <button type="submit" class="btn btn-primary pull-right">Export All Team Quarter Data</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    @endif

</div>
@endsection
