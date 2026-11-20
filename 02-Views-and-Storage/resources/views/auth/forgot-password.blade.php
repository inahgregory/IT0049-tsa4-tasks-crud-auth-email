@extends('layouts.app')
@section('title', 'Forgot password')
@section('content')
<div class="auth-shell"><div class="page-heading compact"><p class="eyebrow">ACCOUNT RECOVERY</p><h1>Forgot your password?</h1><p class="lead">Enter your email and we will send a password reset link.</p></div>
@if(session('status'))<div class="success-message" role="status">{{ session('status') }}</div>@endif
@include('partials.form-errors')
<form class="account-form" method="POST" action="{{ route('password.email') }}">@csrf<div class="auth-fields"><div class="field"><label for="email">Email</label><input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="email"></div><button class="button">Send reset link</button><a class="text-link" href="{{ route('login') }}">Back to login</a></div></form></div>
@endsection

