<?php
use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Facades\Artisan;

Artisan::command('tasks:summary', function () {
    $this->info('Tasks for Today - database summary');
    $this->line('Today: '.today()->toDateString().' ('.config('app.timezone').')');
    $tasks = Task::orderBy('task_date')->orderBy('id')->get();
    $this->table(['ID', 'Title', 'Date', 'Status', 'Archived'], $tasks->map(fn ($task) => [
        $task->id, $task->title, $task->task_date->toDateString(), $task->status,
        $task->is_archived ? 'yes' : 'no',
    ])->all());
    $this->line('Stored records (including archived): '.$tasks->count());
    $this->line('Active tasks: '.Task::active()->count());
    $this->line('Archived tasks: '.Task::where('is_archived', true)->count());
    $this->line('Active tasks today: '.Task::active()->forDate(today())->count());
    $this->line('User records: '.User::count());
})->purpose('Show task status and archiving evidence for the assessment');
