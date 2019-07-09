@extends('emails.master')

@section('content')

<p>Hi {{ $expert->present()->fullname() }},</p>

<p>
    You've received a new message from {{ $trader->present()->fullname() }} on the Alegrant website.
    Please login here (<a href="{{ URL::to(env('WEB_LOGIN_URL')) }}"  style="color: #3C8DBC">{{ env('WEB_LOGIN_URL') }}</a>) and navigate to the Chat section to see their message and respond.
</p>
@endsection
