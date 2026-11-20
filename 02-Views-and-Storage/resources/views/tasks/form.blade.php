@extends('layouts.app')
@section('title', $task->exists ? 'Edit Task' : 'Create Task')
@section('content')
<section class="page-intro compact"><div><a class="text-link" href="{{ route('tasks') }}">&larr; Task List</a><p class="eyebrow form-eyebrow">YOUR NEXT SMALL STEP</p><h1>{{ $task->exists ? 'Edit task' : 'Create task' }}<span>.</span></h1><p class="lead">Give your task a clear title, a date, and a status.</p></div></section>
@include('partials.form-errors')
<form class="account-form" method="POST" action="{{ $task->exists ? route('tasks.update', $task) : route('tasks.store') }}">@csrf
@if($task->exists) @method('PUT') @endif
<div class="form-header"><h2>Task details</h2><p>All fields are required.</p></div>
<div class="auth-fields">
<div class="field"><label for="title">Task title</label><input id="title" name="title" value="{{ old('title', $task->title) }}" required maxlength="255" autofocus @error('title') aria-invalid="true" aria-describedby="title-error" @enderror>@error('title')<p class="field-error" id="title-error">{{ $message }}</p>@enderror</div>
<div class="task-form-row"><div class="field"><label for="task_date">Task date</label><input id="task_date" type="date" name="task_date" value="{{ old('task_date', $task->task_date?->toDateString()) }}" required>@error('task_date')<p class="field-error">{{ $message }}</p>@enderror</div>
<div class="field"><label for="status">Status</label><select id="status" name="status" required>@foreach(['pending' => 'Pending', 'in_progress' => 'In progress', 'done' => 'Done'] as $value => $label)<option value="{{ $value }}" @selected(old('status', $task->status ?? 'pending') === $value)>{{ $label }}</option>@endforeach</select>@error('status')<p class="field-error">{{ $message }}</p>@enderror</div></div>
@if($task->exists)
<p class="field-hint">Changing the status to Done generates a completion email. Saving an already-done task does not send another email.</p>
@endif
</div><div class="form-actions"><button class="button" type="submit">{{ $task->exists ? 'Save changes' : 'Create task' }}</button><a class="button secondary" href="{{ route('tasks') }}">Cancel</a></div>
</form>
@endsection
