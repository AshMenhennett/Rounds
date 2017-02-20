
@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <a href="{{ route('coach.players.index', $team) }}">&laquo; Players</a>
            <br />
            <div class="panel panel-default">
                <div class="panel-heading">Edit player for {{ strtoupper($team->name) }}</div>

                <div class="panel-body">
                    <form action="{{ route('coach.player.update', [$team, $player]) }}" method="post">
                        <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                            <label for="name" class="label-control">Name</label>
                            <input type="text" name="name" id="name" class="form-control" value="{{ (old('name') ? old('name') : $player->name ) }}" required>
                            @if ($errors->has('name'))
                                <div class="help-block danger">
                                    {{ $errors->first('name') }}
                                </div>
                            @endif
                        </div>
                        <div class="form-group checkbox pull-right">
                            <label for="temp">
                                <input type="checkbox" name="temp" id="temp" style="margin-top:4px; padding:8px;" {{ ($player->temp ? 'checked' : '') }}> Temporary Player?
                            </label>
                        </div>
                        <br />
                        <br />

                        {{ csrf_field() }}
                        {{  method_field('PUT') }}
                        <button type="submit" class="btn btn-primary pull-right btn-ok-cancel-relation">Update</button>
                        <a href="{{ route('coach.players.index', $team) }}" class="btn btn-default pull-right btn-ok-cancel-relation">Cancel</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
