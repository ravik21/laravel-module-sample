@extends('emails.master')

@section('content')
<p>Hi {{ $user->present()->fullname() }},</p>

<p>
    Your request to gain access to Alegrant platform has been rejected. If you would like to appeal this decision or re-apply, please contact us on <a href="{{ env('MAIL_FROM_ADDRESS') }}">{{ env('MAIL_FROM_ADDRESS') }}</a>.
</p>
@endsection
