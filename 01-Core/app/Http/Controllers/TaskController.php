<?php
namespace App\Http\Controllers;
use App\Http\Requests\TaskRequest;
use App\Mail\TaskCompleted;
use App\Models\Task;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
class TaskController extends Controller
{
    public function welcome(): View
    {
        $today = today();
        $tasks = Task::active()->forDate($today)->orderBy('id')->get();
        return view('welcome', compact('tasks', 'today'));
    }
    public function index(): View
    {
        $tasks = Task::active()->orderBy('task_date')->orderBy('id')->get();
        return view('tasks', compact('tasks'));
    }
    public function create(): View
    {
        return view('tasks.form', ['task' => new Task(['task_date' => today()])]);
    }
    public function store(TaskRequest $request): RedirectResponse
    {
        Task::create($request->validated());
        return redirect()->route('tasks')->with('success', 'Task created successfully.');
    }
    public function edit(Task $task): View
    {
        abort_if($task->is_archived, 404);
        return view('tasks.form', compact('task'));
    }
    public function update(TaskRequest $request, Task $task): RedirectResponse
    {
        abort_if($task->is_archived, 404);
        $data = $request->validated();
        try {
            $notified = DB::transaction(function () use ($task, $data) {
                // Re-read under the lock before deciding whether the status changed.
                $current = Task::whereKey($task->id)->lockForUpdate()->firstOrFail();
                abort_if($current->is_archived, 404);
                $becameDone = $current->status !== 'done' && $data['status'] === 'done';
                $current->update($data);
                if ($becameDone) {
                    Mail::to(config('tasks.completion_recipient'))->send(new TaskCompleted($current));
                }
                return $becameDone;
            });
        } catch (TransportExceptionInterface $exception) {
            report($exception);
            return back()->withInput()->withErrors([
                'notification' => 'The completion email could not be generated. The task was not changed. Please try again.',
            ]);
        }
        return redirect()->route('tasks')->with('success', $notified
            ? 'Task updated. Completion email generated successfully.'
            : 'Task updated successfully.');
    }
    public function destroy(Request $request, Task $task): RedirectResponse
    {
        abort_if($task->is_archived, 404);
        $request->validate(['confirm_archive' => ['accepted']]);
        $task->forceFill(['is_archived' => true])->save();
        return redirect()->route('tasks')->with('success', 'Task archived. Its record remains in the database.');
    }
}
