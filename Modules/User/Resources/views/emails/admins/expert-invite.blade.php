@extends('emails.master')

@section('content')
  <p>Hi {{$email}},</p>
  <p>You have been invited to join Alegrant as an Expert. Please sign up here to gain access to the Alegrant Hub and start building your profile: <a href="{{$url}}">{{$url}}</a></p>
  <p>We’ll send you additional information in a separate email and in the meantime, if you have any question or if we can be of any assistance just send us a note at <a href="mailto:experts@alegrant.com">experts@alegrant.com</a>.</p>
@endsection
