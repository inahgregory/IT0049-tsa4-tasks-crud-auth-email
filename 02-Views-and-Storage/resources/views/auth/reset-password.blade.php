@extends('layouts.app')
@section('title', 'Reset password')
@section('content')
<div class="auth-shell"><div class="page-heading compact"><p class="eyebrow">ACCOUNT RECOVERY</p><h1>Reset password</h1></div>
@include('partials.form-errors')
<form class="account-form" method="POST" action="{{ route('password.store') }}">@csrf
<input type="hidden" name="token" value="{{ $request->route('token') }}">
<div class="auth-fields"><div class="field"><label for="email">Email</label><input id="email" type="email" name="email" value="{{ old('email', $request->email) }}" required autocomplete="username"></div><div class="field"><label for="password">New password</label><input id="password" type="password" name="password" required autocomplete="new-password" minlength="8"></div><div class="field"><label for="password_confirmation">Confirm password</label><input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password"></div><button class="button">Reset password</button></div></form></div>
@endsection

