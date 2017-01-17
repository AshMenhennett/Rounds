@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <a href="{{ route('team.manage', $team) }}">&laquo; Team Management</a>
            <br />
            <div class="panel panel-default">
                <div class="panel-heading">Players for {{ strtoupper($team->name) }}</div>

                <div class="panel-body">
                    <players team="{{ $team->slug }}"></players>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
