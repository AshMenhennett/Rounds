@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Team Management</div>

                <div class="panel-body">
                    <a href="{{ route('players.index', $team) }}" class="btn btn-default">Add Players</a>
                    <a href="{{ route('rounds.index', $team) }}" class="btn btn-primary">Fill in Round</a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Stats for {{ strtoupper($team->name) }}</div>

                <div class="panel-body">
                    <p>Coach: <strong>{{ $team->user->name() }}</strong>.</p>
                    <p>{{ strtoupper($team->name) }} has played in <strong>{{ count($team->rounds) }}</strong> rounds.</p>
                    <p>{{ strtoupper($team->name) }} has <strong>{{ count($team->players) }}</strong> players.</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Leave Team</div>

                <div class="panel-body">
                    <form action="{{ route('team.user.detach', $team) }}" method="post">
                        {{ csrf_field() }}
                        {{ method_field('DELETE') }}
                        <button type="submit" class="btn btn-danger">Leave Team</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
