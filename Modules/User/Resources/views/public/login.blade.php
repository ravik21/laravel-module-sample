@extends('layouts.account')

@section('title')
    {{ trans('user::auth.login') }} | @parent
@stop

@section('content')
    <div class="login-box-body">
        <h4 class="form-title">{{ trans('user::auth.sign in welcome message') }}</h4>
        @include('partials.notifications')
        {!! Form::open(['route' => 'login.post']) !!}
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

            <div class="el-form-item is-required {{ $errors->has('password') ? ' is-error' : '' }}">
                <label class="el-form-item__label">{{ trans('user::auth.password') }}</label>
                <div class="el-form-item__content">
                    <div class="el-input">
                        <input type="password" autocomplete="off" name="password" class="el-input__inner" value="{{ old('password')}}"/>
                    </div>
                    <div class="el-form-item__error">
                        {!! $errors->first('password', ':message') !!}
                    </div>
                </div>
            </div>
            <div class="el-form-item">
                <div class="el-form-item__content">
                    <!-- <div class="checkbox icheck">
                        <label>
                            <input type="checkbox" name="remember_me"> {{ trans('user::auth.remember me') }}
                        </label>
                    </div> -->
                    <p class="terms">By signing in, you agree to our <a target="_blank" href="{{ route('terms') }}">Terms</a> and that you have read our <a target="_blank" href="{{ route('privacy') }}">Privacy Policy</a>, including our <a target="_blank" href="{{ route('cookies') }}">Cookie Use</a>.</p>
                    <input type="hidden" name="client_id" value="1">
                    <button type="submit" class="btn-block el-button el-button--success">
                        {{ trans('user::auth.login') }}
                    </button>
                </div>
            </div>
        </form>
        <a href="{{ route('reset')}}" class="reset">{{ trans('user::auth.forgot password') }}</a>
    </div>
@stop
