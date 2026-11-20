@extends('layouts.app')
@section('title', 'Verify email')
@section('content')
<div class="auth-shell"><div class="page-heading compact"><p class="eyebrow">EMAIL VERIFICATION</p><h1>Check your email</h1><p class="lead">Open the verification link sent to your email. You can request another message below.</p></div>
@if(session('status') === 'verification-link-sent')<div class="success-message" role="status">A new verification link has been sent.</div>@endif
<form method="POST" action="{{ route('verification.send') }}">@csrf<button class="button">Resend verification email</button></form></div>
@endsection

