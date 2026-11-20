@extends('layouts.app')
@section('title', 'Confirm password')
@section('content')
<div class="auth-shell"><div class="page-heading compact"><p class="eyebrow">SECURITY CHECK</p><h1>Confirm your password</h1><p class="lead">Please confirm your password before continuing.</p></div>
@include('partials.form-errors')
<form class="account-form" method="POST" action="{{ route('password.confirm') }}">@csrf<div class="auth-fields"><div class="field"><label for="password">Password</label><input id="password" type="password" name="password" required autocomplete="current-password"></div><button class="button">Confirm</button></div></form></div>
@endsection

