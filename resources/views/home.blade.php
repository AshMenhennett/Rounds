@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">

            @if (Session::get('registered'))
                <div class="alert alert-success text-center">
                    <p><strong>Congratulations!</strong> Your account has been created.</p>
                    <p>Get started by finding a Team below.</p>
                </div>
            @endif

            <div class="panel panel-default">
                <div class="panel-heading">Dashboard</div>

                <div class="panel-body">
                    @if (count($team))
                        <div class="team">
                            <h4>{{ strtoupper($team->name) }}</h4>
                            <p><a href="{{ route('team.manage', $team) }}" class="btn btn-default">Manage</a></p>
                        </div>
                    @else
                        <p class="text-center">You do not have a team.</p>
                        <p class="text-center"><a href="{{ route('teams.index') }}" class="btn btn-primary">Find a team</a></p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
