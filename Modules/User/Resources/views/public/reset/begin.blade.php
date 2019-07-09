@extends('layouts.account')

@section('title')
    {{ trans('user::auth.reset password') }} | @parent
@stop

@section('content')
    <div class="login-box-body">
        <h4 class="form-title">
            {{ trans('user::auth.to reset password complete this form') }}
        </h4>
        @include('partials.notifications')

        {!! Form::open(['route' => 'reset.post']) !!}
            <div class="el-form-item is-required {{ $errors->has('email') ? ' is-error' : '' }}">
                <label class="el-form-item__label">{{ trans('user::auth.email') }}</label>
                <div class="el-form-item__content">
                    <div class="el-input">
                        <input type="text" autocomplete="off" name="email" class="el-input__inner" value="{{ old('email')}}"/>
                    </div>
                    <div class="el-form-item__error">
                        {!! $errors->first('email', ':message') !!}
                    </div>
                </div>
            </div>
            <div class="el-form-item has-feedback">
                <div class="el-form-item__content">
                    <button type="submit" class="btn-block el-button el-button--success">
                        {{ trans('user::auth.reset password') }}
                    </button>
                </div>
            </div>
        {!! Form::close() !!}

        <a href="{{ route('login') }}">{{ trans('user::auth.I remembered my password') }}</a>
    </div>
@stop
