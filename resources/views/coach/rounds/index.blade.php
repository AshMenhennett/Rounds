@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
<<<<<<< HEAD:resources/views/coach/rounds/index.blade.php
            <a href="{{ route('coach.team.manage', $team) }}">&laquo; Team Management</a>
=======
            <a href="{{ route('team.manage', $team) }}">&laquo; Team Management</a>
>>>>>>> ba045595f44a630f23913d926284dcd1f49686e3:resources/views/rounds/index.blade.php
            <br />

            <div class="panel panel-default">
                <div class="panel-heading">Select a Round to fill in</div>

                <div class="panel-body">
                    @if (count($rounds))
                        <ul class="list-group">
                        @foreach ($rounds as $round)
                            <li class="list-group-item round">
                                <h4>
                                    <a href="{{ route('coach.round.show', [$team, $round]) }}">Fill in Round {{ $round->name }}</a>
                                    <small class="pull-right"><strong>Date</strong>: {{ Carbon\Carbon::createFromTimeStamp(strtotime($round->date($team)))->format('d/m/y') }}</small>
                                </h4>
                            </li>
                        @endforeach
                        </ul>
                    @else
                        <p>There are currently no rounds available.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
