@extends('layouts.account')

@section('title')
    {{ trans('user::auth.account verified') }} | @parent
@stop

@section('content')
    <div class="login-box-body">
        {!! $message !!}
    </div>
@stop
