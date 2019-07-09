@extends('emails.master')

@section('content')
    <h3>{{ trans('user::email.content.heading') }}</h3>

    <p>{{ trans('user::email.content.admin invite content', ['role' => $role, 'sitename' => $sitename]) }}</p>

    <p>
        {{ trans('user::email.content.admin invite content accept') }}
        <a href="{{ URL::to('auth/accept-invite/' . $user['id'] . '/' . $activationCode) }}" style="color: #3C8DBC">{{ trans('user::email.content.link here') }}</a>
    </p>

     <a href="{{ URL::to('auth/accept-invite/' . $user['id'] . '/' . $activationCode) }}" style="font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif; box-sizing: border-box; font-size: 14px; color: #FFF; text-decoration: none; line-height: 2; font-weight: bold; text-align: center; cursor: pointer; display: inline-block; border-radius: 5px; text-transform: capitalize; background: #3C8DBC; margin: 0; padding: 0; border-color: #3C8DBC; border-style: solid; border-width: 10px 20px;">{{ trans('user::email.content.admin invite button') }}</a>
@endsection
