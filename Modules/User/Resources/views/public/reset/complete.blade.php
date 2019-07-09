@extends('layouts.account')

@section('title')
    {{ trans('user::auth.reset password') }} | @parent
@stop

@section('content')
    <div class="login-box-body">
        <p class="login-box-msg">{{ trans('user::auth.reset password') }}</p>
        @include('partials.notifications')

        {!! Form::open() !!}
        <div class="el-form-item is-required {{ $errors->has('password') ? ' is-error' : '' }}">
                <label class="el-form-item__label">{{ trans('user::auth.password') }}</label>
                <div class="el-form-item__content">
                    <div class="el-input">
                        <input type="password" autocomplete="off" name="password" class="el-input__inner" value="{{ old('password')}}" />
                    </div>
                    <div class="el-form-item__error">
                        {!! $errors->first('password', ':message') !!}
                    </div>
                </div>
            </div>
            <div class="el-form-item is-required {{ $errors->has('password_confirmation') ? ' is-error' : '' }}">
                <label class="el-form-item__label">{{ trans('user::auth.password') }}</label>
                <div class="el-form-item__content">
                    <div class="el-input">
                        <input type="password" autocomplete="off" name="password_confirmation" class="el-input__inner" value="{{ old('password_confirmation')}}"/>
                    </div>
                    <div class="el-form-item__error">
                        {!! $errors->first('password_confirmation', ':message') !!}
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
    </div>
@stop
