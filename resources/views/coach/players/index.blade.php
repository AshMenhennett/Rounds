@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <a href="{{ route('coach.team.manage', $team) }}">&laquo; Team Management</a>
            <br />
            <div class="panel panel-default">
                <div class="panel-heading">Players for {{ strtoupper($team->name) }}</div>

                <div class="panel-body">
<<<<<<< HEAD:resources/views/coach/players/index.blade.php
                    <coach-team-players team="{{ $team->slug }}"></coach-team-players>
=======
                    <team-players team="{{ $team->slug }}"></team-players>
>>>>>>> ba045595f44a630f23913d926284dcd1f49686e3:resources/views/players/index.blade.php
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
