@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <a href="{{ route('team.show', $team) }}">&laquo; Team Management</a>
            <br />
            <div class="panel panel-default">
                <div class="panel-heading">Round {{ $round->name }} for {{ strtoupper($team->name) }}</div>

                <div class="panel-body">

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
