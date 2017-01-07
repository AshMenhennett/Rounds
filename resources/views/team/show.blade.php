@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Pick a Round to fill in</div>

                <div class="panel-body">
                    <a href="{{ route('players.index', $team) }}" class="btn btn-primary">Add Players</a>
                    <br />
                    <br />

                    @if (count($rounds))
                        <ul class="list-group">
                        @foreach ($rounds as $round)
                            <li class="list-group-item round">
                                <h4><a href="{{ route('round.index', [$team, $round]) }}">Round {{ $round->name }}</a></h4>
                                @if ($round->date) <span>{{ $round->date }}</span> @endif
                            </li>
                        @endforeach
                        </ul>
                    @else
                        <p>There are currently no rounds available.</p>
                    @endif

                    <br />
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
