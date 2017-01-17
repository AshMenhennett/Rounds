@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Dashboard</div>

                <div class="panel-body">
                    @if (count($team))
                        <div class="team">
                            <h4>{{ strtoupper($team->name) }}</h4>
                            <a href="{{ route('team.manage', $team) }}" class="btn btn-default">Manage</a>
                        </div>
                    @else
                        <p class="text-center">You do not have a team.</p>
                        <a href="{{ route('teams.index') }}" class="btn btn-primary btn-block">Find a team</a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
