@extends('layouts.app')
@section('title', 'Your profile')
@section('content')
<div class="page-heading compact"><p class="eyebrow">YOUR ACCOUNT</p><h1>Profile and security</h1><p class="lead">Update your details and keep your password private.</p></div>
@if(session('status'))<div class="success-message" role="status">{{ session('status') === 'profile-updated' ? 'Profile updated successfully.' : 'Password updated successfully.' }}</div>@endif
@include('partials.form-errors')
<form class="account-form profile-section" method="POST" action="{{ route('account.update') }}">@csrf @method('PATCH')
<div class="form-header"><h2>Profile details</h2></div><div class="form-grid"><div class="field"><label for="full_name">Full name</label><input id="full_name" name="full_name" value="{{ old('full_name', $user->full_name) }}" required autocomplete="name"></div><div class="field"><label for="email">Email</label><input id="email" type="email" name="email" value="{{ old('email', $user->email) }}" required autocomplete="email"></div></div><div class="form-actions"><button class="button">Save profile</button></div></form>
@if($errors->updatePassword->any())<div class="validation-summary" role="alert"><h2>Please check your password</h2><ul>@foreach($errors->updatePassword->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
<form class="account-form profile-section" method="POST" action="{{ route('password.update') }}">@csrf @method('PUT')
<div class="form-header"><h2>Change password</h2></div><div class="auth-fields"><div class="field"><label for="current_password">Current password</label><input id="current_password" type="password" name="current_password" required autocomplete="current-password"></div><div class="field"><label for="new_password">New password</label><input id="new_password" type="password" name="password" required minlength="8" autocomplete="new-password"></div><div class="field"><label for="new_password_confirmation">Confirm new password</label><input id="new_password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password"></div><button class="button">Update password</button></div></form>
@if($errors->userDeletion->any())<div class="validation-summary" role="alert">@foreach($errors->userDeletion->all() as $error)<p>{{ $error }}</p>@endforeach</div>@endif
<details class="danger-zone"><summary>Delete my account</summary><p>This permanently removes your account. Enter your current password to confirm.</p><form method="POST" action="{{ route('account.destroy') }}">@csrf @method('DELETE')<div class="field"><label for="delete_password">Current password</label><input id="delete_password" type="password" name="password" required autocomplete="current-password"></div><button class="button danger-button">Delete my account</button></form></details>
@endsection

