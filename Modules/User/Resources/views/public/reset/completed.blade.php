@extends('layouts.account')

@section('title')
    {{ trans('user::auth.reset password') }} | @parent
@stop

@section('content')
    <div class="login-box-body">
        <div class="alert alert-success fade in">
            {{ trans('user::messages.password reset') }}
        </div>
    </div>
@stop