@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <a href="{{ route('home') }}">&laquo; Your Teams</a>
            <br />

            <div class="panel panel-default">
                <div class="panel-heading">Team Management for Team {{ $team->name }}</div>

                <div class="panel-body">
                    <a href="{{ route('coach.team.players.index', $team) }}" class="btn btn-default">Add Players</a>
                    <a href="{{ route('coach.team.rounds.index', $team) }}" class="btn btn-primary">Fill in Rounds</a>

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
                <div class="panel-heading">Stats for Team {{ strtoupper($team->name) }}</div>

                <div class="panel-body">
                    @if ($team->user)
                        <p>Coach: <strong>{{ $team->user->name() }}</strong>.</p>
                    @else
                        <p><strong>No Coach</strong>.</p>
                    @endif
                    <p>Team {{ strtoupper($team->name) }} has played in <strong>{{ count($team->rounds) }}</strong> rounds.</p>
                    <p>Team {{ strtoupper($team->name) }} has <strong>{{ count($team->players) }}</strong> players.</p>
                </div>
            </div>
        </div>
    </div>

    @if (Auth::user()->isAdmin() && $team->hasCoach() && ! $team->belongsToCoach(Auth::user()))
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">Kick Coach from {{ $team->name }}</div>

                    <div class="panel-body">
                        <form action="{{ route('admin.team.kick.coach', $team) }}" method="post">
                            {{ csrf_field() }}
                            {{ method_field('PUT') }}
                            <button type="submit" class="btn btn-danger">Kick Coach</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if (Auth::user()->hasTeam($team))
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">Leave Team {{ $team->name }}</div>

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
    @endif
</div>
@endsection
