@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">

            @if (Session::get('registered'))
                <div class="alert alert-success text-center">
                    <p><strong>Congratulations!</strong> Your account has been created.</p>
<<<<<<< HEAD
                    <p>Get started by joining a Team below.</p>
=======
                    <p>Get started by finding a Team below.</p>
>>>>>>> ba045595f44a630f23913d926284dcd1f49686e3
                </div>
            @endif

            <div class="panel panel-default">
                <div class="panel-heading">
                    @if (count($team))
                        Joined Teams
                    @else
                        Find a Team
                    @endif
                </div>

                <div class="panel-body">
                    @if (count($team))
                        <div class="team">
                            <h4>{{ strtoupper($team->name) }}</h4>
<<<<<<< HEAD
                            <p><a href="{{ route('coach.team.manage', $team) }}" class="btn btn-default">Manage</a></p>
                        </div>
                    @else
                        <p class="text-center">To get started coaching, you need to join a team.</p>
                        <p class="text-center"><a href="{{ route('coach.teams.index') }}" class="btn btn-primary">Join a team</a></p>
                        <p class="text-center">Feel free to check out the <a href="/faq">FAQ</a> to help you get started.</p>
=======
                            <p><a href="{{ route('team.manage', $team) }}" class="btn btn-default">Manage</a></p>
                        </div>
                    @else
                        <p class="text-center">You do not have a team.</p>
                        <p class="text-center"><a href="{{ route('teams.index') }}" class="btn btn-primary">Find a team</a></p>
>>>>>>> ba045595f44a630f23913d926284dcd1f49686e3
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
