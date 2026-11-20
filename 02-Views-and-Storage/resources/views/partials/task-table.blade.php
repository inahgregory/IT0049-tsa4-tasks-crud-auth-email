<div class="table-wrap" tabindex="0" role="region" aria-label="{{ $tableLabel }}">
<table>
<caption class="sr-only">{{ $tableLabel }}</caption>
<thead><tr><th scope="col">ID</th><th scope="col">Task</th><th scope="col">Date</th><th scope="col">Status</th>@auth<th scope="col">Manage</th>@endauth</tr></thead>
<tbody>
@forelse($tasks as $task)
<tr>
<td class="muted">{{ str_pad($task->id, 2, '0', STR_PAD_LEFT) }}</td>
<th scope="row">{{ $task->title }}</th>
<td class="date-cell"><time datetime="{{ $task->task_date->toDateString() }}">{{ $task->task_date->format('M d, Y') }}</time></td>
<td><span @class(['status', 'completed' => $task->status === 'done', 'in-progress' => $task->status === 'in_progress'])>{{ ucfirst(str_replace('_', ' ', $task->status)) }}</span></td>
@auth
<td><div class="row-actions"><a class="text-link" href="{{ route('tasks.edit', $task) }}">Edit</a>
<details class="row-archive"><summary>Archive</summary><form method="POST" action="{{ route('tasks.destroy', $task) }}">@csrf @method('DELETE')
<label class="check-label"><input type="checkbox" name="confirm_archive" value="1" required> Confirm archive</label>
<button class="archive-button" type="submit">Archive task</button></form></details></div></td>
@endauth
</tr>
@empty
<tr><td colspan="{{ auth()->check() ? 5 : 4 }}" class="empty-state">{{ $emptyMessage }}</td></tr>
@endforelse
</tbody>
</table>
</div>
