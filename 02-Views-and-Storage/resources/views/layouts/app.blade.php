<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="description" content="Tasks for Today with task management, authentication, and completion notifications.">
<title>@yield('title') | Tasks for Today</title>
<link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
<a class="skip" href="#main">Skip to content</a>
<header class="site-header"><div class="shell topbar">
<a class="brand" href="{{ route('welcome') }}"><span class="brand-icon" aria-hidden="true">&#10003;</span> Tasks for Today<span class="brand-dot">.</span></a>
<nav aria-label="Main navigation">
@foreach (['welcome' => 'Welcome', 'tasks' => 'Task List', 'profile' => 'Profile', 'about' => 'About'] as $route => $label)
<a href="{{ route($route) }}" @class(['nav-link', 'active' => request()->routeIs($route, $route.'.*')])>{{ $label }}</a>
@endforeach
@auth
<a class="nav-link" href="{{ route('account.edit') }}">My account</a>
<form action="{{ route('logout') }}" method="POST" class="logout-form">@csrf<button class="nav-link logout-button" type="submit">Log out</button></form>
@else
<a class="nav-link" href="{{ route('login') }}">Log in</a>
<a class="nav-link" href="{{ route('register') }}">Register</a>
@endauth
</nav></div></header>
<main id="main" class="shell">
@if(session('success'))
<div class="success-message" role="status">{{ session('success') }}</div>
@endif
@yield('content')
</main>
<footer class="shell site-footer"><span>Tasks for Today &middot; A little clarity, every day.</span><span>Inah Angela Gregorio &middot; IT0049 TSA4</span></footer>
</body></html>
