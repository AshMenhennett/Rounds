
@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <a href="{{ route('players.index', $team) }}">&laquo; Players</a>
            <br />
            <div class="panel panel-default">
                <div class="panel-heading">Edit player for {{ strtoupper($team->name) }}</div>

                <div class="panel-body">
                    <form action="{{ route('player.update', [$team, $player]) }}" method="post">
                        <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                            <label for="name" class="label-control">Name</label>
                            <input type="text" name="name" id="name" class="form-control" value="{{ (old('name') ? old('name') : $player->name ) }}" required>
                            @if ($errors->has('name'))
                                <div class="help-block danger">
                                    {{ $errors->first('name') }}
                                </div>
                            @endif
                        </div>
                        <br />
                        <div class="form-group checkbox pull-right">
                            <label for="temp">
                                <input type="checkbox" name="temp" id="temp" style="margin-top:4px; padding:8px;" {{ ($player->temp ? 'checked' : '') }}> Temporary Player?
                            </label>
                        </div>
                        <br />
                        <br />

                        {{ csrf_field() }}
                        <button type="submit" class="btn btn-default pull-right">Update</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
