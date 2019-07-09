@extends('emails.master')

@section('content')
<p>The following Expert has registered on the Alegrant website and is awaiting approval:</p>

<ul>
    <li>Email: {{ $user->email }}</li>
    <li>First name: {{ $user->first_name }}</li>
    <li>Last name: {{ $user->last_name }}</li>
    <li>Country: {{ $user->companyCountry->name }}</li>
</ul>


<p>
    Go to the dashboard for making approval.
    <a href="{{ URL::to('admin/user/experts/' . $user->id) }}" style="color: #3C8DBC">Click here!</a>
</p>

@endsection
