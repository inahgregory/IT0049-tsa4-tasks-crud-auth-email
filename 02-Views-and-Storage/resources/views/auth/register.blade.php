@extends('layouts.app')
@section('title', 'Register')
@section('content')
<div class="auth-shell"><div class="page-heading compact"><p class="eyebrow">YOUR PERSONAL WORKSPACE</p><h1>Create your account</h1><p class="lead">Register to access task management.</p></div>
@include('partials.form-errors')
<form class="account-form" method="POST" action="{{ route('register') }}">@csrf
<div class="auth-fields">
<div class="field"><label for="username">Username</label><input id="username" name="username" value="{{ old('username') }}" required maxlength="50" autocomplete="username" autofocus></div>
<div class="field"><label for="full_name">Full name</label><input id="full_name" name="full_name" value="{{ old('full_name') }}" required maxlength="255" autocomplete="name"></div>
<div class="field"><label for="email">Email</label><input id="email" type="email" name="email" value="{{ old('email') }}" required maxlength="255" autocomplete="email"><p class="field-hint">Use lowercase letters for your email address.</p></div>
<div class="field"><label for="password">Password</label><input id="password" type="password" name="password" required minlength="8" autocomplete="new-password"><p class="field-hint">Use at least 8 characters. Choose your own password.</p></div>
<div class="field"><label for="password_confirmation">Confirm password</label><input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password"></div>
<button class="button" type="submit">Register</button>
</div></form><p class="auth-note">Already registered? <a href="{{ route('login') }}">Log in</a>.</p></div>
@endsection


