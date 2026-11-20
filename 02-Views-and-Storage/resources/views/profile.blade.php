@extends('layouts.app')
@section('title', 'Profile')
@section('content')
<section class="page-intro compact"><div><p class="eyebrow">THE PERSON BEHIND THE PLAN</p><h1>Your profile<span>.</span></h1><p class="lead">A familiar name behind your everyday progress.</p></div></section>
@if ($user)
<section class="profile-grid">
    <article class="profile-card"><div class="avatar" aria-hidden="true">{{ mb_substr($user->full_name, 0, 1) }}</div><span class="eyebrow">PERSONAL WORKSPACE</span><h2>{{ $user->full_name }}</h2><p>&#64;{{ $user->username }}</p><span class="count-chip">Student developer</span></article>
    <article class="panel details"><p class="eyebrow">ACCOUNT DETAILS</p><h2>A little about you</h2><dl><div><dt>Full name</dt><dd>{{ $user->full_name }}</dd></div><div><dt>Username</dt><dd>{{ $user->username }}</dd></div><div><dt>Email</dt><dd>{{ $user->email }}</dd></div><div><dt>Member since</dt><dd>{{ $user->created_at->format('F d, Y') }}</dd></div></dl><p class="panel-note">The email address is a sample address for this assessment.</p></article>
</section>
@else
<section class="panel details"><h2>No profile available</h2><p>The user profile has not been set up yet.</p></section>
@endif
@endsection
