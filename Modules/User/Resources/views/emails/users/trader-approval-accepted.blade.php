@extends('emails.master')

@section('content')

<p>Hi {{ $user->present()->fullname() }},</p>

<p>Welcome to the Alegrant Hub! Please follow this link (include link to sign-in page) to sign in to alegrant.com, complete your profile and access experts.</p>
<br>
<p>We’ll send you additional information about using the Alegrant Hub in a separate email and in the meantime, if you have any question or if we can be of any assistance just send us a note at <a href="mailto:info@alegrant.com">info@alegrant.com</a>, we’ll be happy to help.</p>
<br>
<p>Follow this link to login to the platform and get access to Global Trade and Customs Compliance experts: <a href="{{ URL::to(env('WEB_LOGIN_URL')) }}"  style="color: #3C8DBC">{{ env('WEB_LOGIN_URL') }}</a></p>



</p>
@endsection
