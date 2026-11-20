@extends('layouts.app')
@section('title', 'Log in')
@section('content')
<div class="auth-shell"><div class="page-heading compact"><p class="eyebrow">WELCOME BACK</p><h1>Log in to Tasks for Today</h1><p class="lead">Sign in to manage tasks.</p></div>
@if (session('status'))<div class="success-message" role="status">{{ session('status') }}</div>@endif
@include('partials.form-errors')
<form class="account-form" action="{{ route('login') }}" method="POST">@csrf
<div class="auth-fields">
<div class="field"><label for="email">Email</label><input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username"></div>
<div class="field"><label for="password">Password</label><input id="password" type="password" name="password" required autocomplete="current-password"></div>
<label class="check-label"><input type="checkbox" name="remember" value="1" @checked(old('remember'))> Remember me</label>
<button type="submit" class="button">Log in</button>
<a class="text-link" href="{{ route('password.request') }}">Forgot your password?</a>
</div></form><p class="auth-note">New here? <a href="{{ route('register') }}">Create an account</a>.</p></div>
@endsection

