@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">
                    Create some buttons to show on the <a href="{{ config('app.url') . '/ecosystem' }}">ecosystem</a> page.
                </div>

                <div class="panel-body">
                    <form action="{{ route('admin.ecosystem.button.store') }}" enctype="multipart/form-data" method="post">
                        <div class="form-group{{ $errors->has('value') ? ' has-error' : '' }}">
                            <label for="value" class="label-control">Button Text</label>
                            <input type="text" name="value" id="value" class="form-control" value="{{ (old('value') ? old('value') : '' ) }}" placeholder="Goto My Cool site">
                            @if ($errors->has('value'))
                                <div class="help-block danger">
                                    {{ $errors->first('value') }}
                                </div>
                            @endif
                        </div>

                        <div class="form-group{{ $errors->has('link') ? ' has-error' : '' }}">
                            <label for="link" class="label-control">Button Link</label>
                            <input type="text" name="link" id="link" class="form-control" value="{{ (old('link') ? old('link') : '' ) }}" placeholder="http://google.com">
                            @if ($errors->has('link'))
                                <div class="help-block danger">
                                    {{ $errors->first('link') }}
                                </div>
                            @endif
                        </div>

                        <div class="help-block">
                            <p>Use a link OR a file for the destination of the button.<br />Using a link and a file will result in the file being used, instead of the link.</p>
                        </div>

                        <div class="form-group{{ $errors->has('file') ? ' has-error' : '' }}">
                            <label for="file" class="label-control">File to link to</label>
                            <input type="file" name="file" id="file" class="form-control">
                            @if ($errors->has('file'))
                                <div class="help-block danger">
                                    {{ $errors->first('file') }}
                                </div>
                            @endif
                        </div>

                        {{ csrf_field() }}
                        <button type="submit" class="btn btn-primary pull-right btn-ok-cancel-relation">Add Button</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">
                    Ecosystem Buttons
                </div>

                <div class="panel-body">
                    <admin-ecosystem-buttons buttons-prop="{{ $buttons }}"></admin-ecosystem-buttons>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
