@extends('emails.master')

@section('content')
<p>Hi {{ $user->present()->fullname() }},</p>

<p>Your account has been approved. Follow this link to login to the site to complete your profile and be found by traders worldwide: <a href="http://alegrant.com/login">http://alegrant.com/login</a></p>

<p>If you have any question or if we can be of any assistance just send us a note at <a href="mailto:experts@alegrant.com">experts@alegrant.com</a>.</p>

@endsection
