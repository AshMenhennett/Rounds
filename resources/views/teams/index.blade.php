@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Choose a Team</div>
                <div class="panel-body">
                    @if (count($teams))
                        @foreach ($teams as $team)
                            <div class="team">
                                <h4>{{ $team->name }}</h4>
                                <form action="{{ route('team.store', $team) }}" method="post">
                                    {{ csrf_field() }}
                                    <button type="submit" class="btn btn-default">Join</button>
                                </form>
                            </div>
                        @endforeach
                    @else
                        <p class="text-center">There are currently no available teams.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
