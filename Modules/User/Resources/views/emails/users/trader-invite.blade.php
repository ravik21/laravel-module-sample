@extends('emails.master')

@section('content')
  <p>
    You have been invited to join the Alegrant website as a Trader by {{ $inviter->present()->fullname() }}. Please sign up here to gain access to the website: <a href="{{ URL::to(env('TRADER_WEB_SIGNUP_URL')) }}"  style="color: #3C8DBC">{{ env('TRADER_WEB_SIGNUP_URL') }}</a>
  </p>


  <a href="{{ URL::to(env('TRADER_WEB_SIGNUP_URL')) }}" style="font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif; box-sizing: border-box; font-size: 14px; color: #FFF; text-decoration: none; line-height: 2; font-weight: bold; text-align: center; cursor: pointer; display: inline-block; border-radius: 5px; text-transform: capitalize; background: #3C8DBC; margin: 0; padding: 0; border-color: #3C8DBC; border-style: solid; border-width: 10px 20px;">Sign up</a>
@endsection
