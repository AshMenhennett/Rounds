
@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <a href="{{ route('admin.home') }}">&laquo; Dashboard</a>
            <br />
            <div class="panel panel-default">
                <div class="panel-heading">Edit {{ strtoupper($team->name) }}</div>

                <div class="panel-body">
                    <form action="{{ route('admin.team.update', [$team, 'v=' . Request::get('v')]) }}" method="post">
                        <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                            <label for="name" class="label-control">Name</label>
                            <input type="text" name="name" id="name" class="form-control" value="{{ (old('name') ? old('name') : $team->name ) }}" required>
                            @if ($errors->has('name'))
                                <div class="help-block danger">
                                    {{ $errors->first('name') }}
                                </div>
                            @endif
                        </div>
                        <div class="form-group{{ $errors->has('slug') ? ' has-error' : '' }}">
                            <label for="slug" class="label-control">Slug</label>
                            <div class="input-group">
                                <span class="input-group-addon">{{ env('APP_URL') . '/teams/'}}</span>
                                <input type="text" name="slug" id="slug" class="form-control" value="{{ (old('slug') ? old('slug') : $team->slug ) }}" required>
<<<<<<< HEAD
                                <span class="input-group-addon">/manage</span>
=======
>>>>>>> ba045595f44a630f23913d926284dcd1f49686e3
                            </div>
                            @if ($errors->has('slug'))
                                <div class="help-block danger">
                                    {{ $errors->first('slug') }}
                                </div>
                            @endif
                        </div>
                        <div class="help-block">
                            <p>This URL is only accessible by the coach and admin.</p>
                        </div>

                        {{ csrf_field() }}
                        {{  method_field('PUT') }}
                        <button type="submit" class="btn btn-primary pull-right btn-ok-cancel-relation">Update</button>
<<<<<<< HEAD
                        <a href="{{ route('admin.home', [$team, 'v=' . Request::get('v')]) }}" class="btn btn-default pull-right btn-ok-cancel-relation">Cancel</a>
=======
                        <a href="{{ route('admin.home') }}" class="btn btn-default pull-right btn-ok-cancel-relation">Cancel</a>
>>>>>>> ba045595f44a630f23913d926284dcd1f49686e3
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
