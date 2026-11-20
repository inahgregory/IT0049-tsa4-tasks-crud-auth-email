@extends('layouts.app')
@section('title', 'Welcome')
@section('content')
<section class="page-intro">
    <div><p class="eyebrow">YOUR DAILY OVERVIEW</p><h1>Make room for<br>what matters<span>.</span></h1><p class="lead">One day at a time. Here is what is on your list today.</p></div>
    <aside class="date-card"><span class="eyebrow">TODAY</span><strong>{{ $today->format('d') }}</strong><span>{{ $today->format('l, F Y') }}</span><small>{{ config('app.timezone') }} &middot; UTC+08:00</small></aside>
</section>
@auth<div class="welcome-manage"><span class="count-chip">Signed in as {{ auth()->user()->full_name }}</span><a class="button" href="{{ route('tasks.create') }}">Create task &rarr;</a></div>@endauth
<section class="stats" aria-label="Today's task summary">
    <article><span>Tasks today</span><strong>{{ $tasks->count() }}</strong><small>On your daily list</small></article>
    <article><span>Completed</span><strong>{{ $tasks->where('status', 'done')->count() }}</strong><small>A little progress counts</small></article>
    <article><span>Still to do</span><strong>{{ $tasks->where('status', '!=', 'done')->count() }}</strong><small>Take your next small step</small></article>
</section>
<section class="panel">
    <div class="panel-heading"><div><p class="eyebrow">FOCUS FOR THE DAY</p><h2>Today's tasks</h2></div><a class="text-link" href="{{ route('tasks') }}">View all tasks &rarr;</a></div>
    @include('partials.task-table', ['tableLabel' => "Today's tasks", 'emptyMessage' => 'No tasks scheduled for today. Enjoy the breathing room.'])
    <p class="panel-note">Showing tasks for {{ $today->format('F d, Y') }} only, including completed tasks.</p>
</section>
@endsection


