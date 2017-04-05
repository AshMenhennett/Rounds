@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <a href="{{ route('coach.team.manage', $team) }}">&laquo; Team Management</a>
            <br />
            <div class="panel panel-default">
                <div class="panel-heading">Players for Team {{ strtoupper($team->name) }}</div>

                <div class="panel-body">
                    <coach-team-players team="{{ $team->slug }}"></coach-team-players>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
