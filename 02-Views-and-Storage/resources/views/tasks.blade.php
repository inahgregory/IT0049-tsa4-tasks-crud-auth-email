@extends('layouts.app')
@section('title', 'Task List')
@section('content')
<section class="page-intro compact"><div><p class="eyebrow">THE COMPLETE PICTURE</p><h1>Your task list<span>.</span></h1><p class="lead">Every active task, in date order. Archived tasks are kept out of the way.</p></div><div class="heading-actions"><span class="count-chip">{{ $tasks->count() }} tasks total</span>@auth<a class="button" href="{{ route('tasks.create') }}">Create task &rarr;</a>@endauth</div></section>
@guest
<p class="guest-note">You can browse every active task. <a href="{{ route('login') }}">Log in</a> to create, edit, or archive tasks.</p>
@endguest
@include('partials.form-errors')
<section class="panel"><div class="panel-heading"><div><p class="eyebrow">ALL DATES</p><h2>All tasks</h2></div><span class="muted">Earliest date first</span></div>
@include('partials.task-table', ['tableLabel' => 'All tasks ordered by date', 'emptyMessage' => 'No tasks have been added yet.'])
<p class="panel-note">All dates and statuses are included. Archived records remain in the database.</p></section>
@endsection
