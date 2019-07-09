@extends('layouts.legal')

@section('title')
    {{ trans('user::auth.privacy') }} | @parent
@stop

@section('content')
    <div class="col-sm-12">
        <h2>Privacy Policy</h2>
        <p>Please refer to British Cycling’s Data Privacy Notice, which can be found here: <a target="_blank" href="https://www.britishcycling.org.uk/privacynotice">https://www.britishcycling.org.uk/privacynotice</a></p>
    </div>
@stop
