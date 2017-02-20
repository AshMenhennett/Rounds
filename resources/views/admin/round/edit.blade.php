
@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <a href="{{ route('admin.home') }}">&laquo; Dashboard</a>
            <br />
            <div class="panel panel-default">
                <div class="panel-heading">Edit Round {{ $round->name }}</div>

                <div class="panel-body">
                    <form action="{{ route('admin.round.update', [$round, 'v=' . Request::get('v')]) }}" method="post">
                        <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                            <label for="name" class="label-control">Name</label>
                            <input type="text" name="name" id="name" class="form-control" value="{{ (old('name') ? old('name') : $round->name ) }}" required>
                            @if ($errors->has('name'))
                                <div class="help-block danger">
                                    {{ $errors->first('name') }}
                                </div>
                            @endif
                        </div>

                        <div class="form-group{{ $errors->has('date') ? ' has-error' : ''}}">
                            <span><strong>Current Set Date</strong>: {{ Carbon\Carbon::createFromTimeStamp(strtotime($round->default_date))->format('d/m/Y') }}.</span>
                            <input type="date" name="date" class="form-control" value="{{ old('date') ? old('date') : '' }}" pattern="[0-9]{2}/[0-9]{2}/[0-9]{4}" />
                            @if ($errors->has('date'))
                                <div class="help-block danger">
                                    Please enter a date in the format: DD/MM/YYYY.
                                </div>
                            @endif
                        </div>

                        {{ csrf_field() }}
                        {{  method_field('PUT') }}
                        <button type="submit" class="btn btn-primary pull-right btn-ok-cancel-relation">Update</button>
                        <a href="{{ route('admin.home', [$round, 'v=' . Request::get('v')]) }}" class="btn btn-default pull-right btn-ok-cancel-relation">Cancel</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
