@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Team Management</div>

                <div class="panel-body">
                    <a href="{{ route('coach.players.index', $team) }}" class="btn btn-default">Add Players</a>
                    <a href="{{ route('coach.rounds.index', $team) }}" class="btn btn-primary">Fill in Rounds</a>

                    @if (Auth::user()->isAdmin())
                        <form class="inline" action="{{ route('admin.team.bestPlayersAllowed.toggle', $team) }}" method="post">
                            {{ csrf_field() }}
                            {{ method_field('PUT') }}
                            <button type="submit" class="btn {{ $team->bestPlayersAllowed() ? 'btn-danger' : 'btn-success' }}">{{ $team->bestPlayersAllowed() ? 'Revoke Best Player Rights' : 'Allow Best Players' }}</button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Stats for {{ strtoupper($team->name) }}</div>

                <div class="panel-body">
                    @if ($team->user)
                        <p>Coach: <strong>{{ $team->user->name() }}</strong>.</p>
                    @else
                        <p><strong>No Coach</strong>.</p>
                    @endif
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
                    <form action="{{ route('coach.team.user.detach', $team) }}" method="post">
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
