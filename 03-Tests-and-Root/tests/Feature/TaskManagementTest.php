<?php
namespace Tests\Feature;
use App\Mail\TaskCompleted;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Symfony\Component\Mailer\Exception\TransportException;
use Tests\TestCase;

class TaskManagementTest extends TestCase
{
    use RefreshDatabase;

    private function taskData(array $changes = []): array
    {
        return array_merge(['title' => 'Test task', 'status' => 'pending', 'task_date' => today()->toDateString()], $changes);
    }
    private function login(): User
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        return $user;
    }

    public function test_four_original_pages_are_public_and_hide_management_controls(): void
    {
        $this->seed();
        foreach (['/', '/tasks', '/profile', '/about'] as $url) {
            $this->get($url)->assertOk()->assertDontSee('Archive task')->assertDontSee('href="'.route('tasks.create').'"', false);
        }
        $this->get('/profile')->assertSee('Inah Angela Gregorio');
    }

    public function test_guests_cannot_create_edit_update_or_archive_tasks(): void
    {
        Mail::fake();
        $task = Task::create($this->taskData());
        foreach ([
            ['GET', '/tasks/create'], ['POST', '/tasks'],
            ['GET', '/tasks/'.$task->id.'/edit'], ['PUT', '/tasks/'.$task->id],
            ['PATCH', '/tasks/'.$task->id], ['DELETE', '/tasks/'.$task->id],
        ] as [$method, $url]) {
            $this->call($method, $url, $this->taskData(['status' => 'done']) + ['confirm_archive' => '1'])
                ->assertRedirect('/login');
        }
        $this->assertDatabaseCount('tasks', 1);
        $this->assertSame('pending', $task->fresh()->status);
        $this->assertFalse($task->fresh()->is_archived);
        Mail::assertNothingSent();
    }

    public function test_authenticated_user_can_create_edit_and_archive_without_deleting_row(): void
    {
        $this->login();
        $this->get('/tasks/create')->assertOk()->assertSee('name="_token"', false);
        $this->post('/tasks', $this->taskData(['title' => 'Created task']))
            ->assertRedirect('/tasks')->assertSessionHas('success');
        $task = Task::firstOrFail();
        $this->get('/tasks/'.$task->id.'/edit')->assertOk()->assertSee('value="Created task"', false);
        $this->put('/tasks/'.$task->id, $this->taskData(['title' => 'Edited task', 'status' => 'in_progress']))
            ->assertRedirect('/tasks');
        $this->get('/tasks')->assertSee('Edited task');
        $this->delete('/tasks/'.$task->id, ['confirm_archive' => '1'])->assertRedirect('/tasks');
        $this->assertDatabaseHas('tasks', ['id' => $task->id, 'is_archived' => 1]);
        $this->assertDatabaseCount('tasks', 1);
        $this->get('/')->assertDontSee('Edited task');
        $this->get('/tasks')->assertDontSee('Edited task');
    }

    public function test_validation_rejects_bad_input_preserves_values_and_does_not_save(): void
    {
        $this->login();
        $this->from('/tasks/create')->post('/tasks', $this->taskData(['title' => 'Keep this title', 'task_date' => 'not-a-date']))
            ->assertRedirect('/tasks/create')->assertSessionHasErrors('task_date')
            ->assertSessionHasInput('title', 'Keep this title');
        $this->get('/tasks/create')->assertSee('value="Keep this title"', false)->assertSee('Please check your details');
        $this->post('/tasks', [])->assertSessionHasErrors(['title', 'status', 'task_date']);
        $this->post('/tasks', $this->taskData(['status' => 'unknown']))->assertSessionHasErrors('status');
        $this->assertDatabaseCount('tasks', 0);
    }

    public function test_done_transition_generates_one_notification_and_done_to_done_does_not_resend(): void
    {
        $this->login();
        Mail::fake();
        config(['tasks.completion_recipient' => 'configured@example.com']);
        $task = Task::create($this->taskData());
        $this->put('/tasks/'.$task->id, $this->taskData(['status' => 'done']))->assertRedirect('/tasks');
        Mail::assertSent(TaskCompleted::class, function ($mail) use ($task) {
            return $mail->hasTo('configured@example.com') && $mail->task->id === $task->id;
        });
        $this->put('/tasks/'.$task->id, $this->taskData(['status' => 'done', 'title' => 'Edited while done']))
            ->assertRedirect('/tasks');
        Mail::assertSentCount(1);
        $this->assertSame('done', $task->fresh()->status);
    }

    public function test_other_updates_and_creating_an_initially_done_task_do_not_notify(): void
    {
        $this->login();
        Mail::fake();
        $task = Task::create($this->taskData());
        $this->put('/tasks/'.$task->id, $this->taskData(['status' => 'in_progress']))->assertRedirect('/tasks');
        $this->post('/tasks', $this->taskData(['title' => 'Created as done', 'status' => 'done']))->assertRedirect('/tasks');
        Mail::assertNothingSent();
    }

    public function test_reopening_and_completing_again_is_a_new_transition(): void
    {
        $this->login();
        Mail::fake();
        $task = Task::create($this->taskData());
        foreach (['done', 'pending', 'done'] as $status) {
            $this->put('/tasks/'.$task->id, $this->taskData(['status' => $status]))->assertRedirect('/tasks');
        }
        Mail::assertSentCount(2);
    }

    public function test_archive_requires_confirmation_and_archived_tasks_cannot_be_edited(): void
    {
        $this->login();
        Mail::fake();
        $task = Task::create($this->taskData());
        $this->delete('/tasks/'.$task->id)->assertSessionHasErrors('confirm_archive');
        $this->assertFalse($task->fresh()->is_archived);
        $this->delete('/tasks/'.$task->id, ['confirm_archive' => '1'])->assertRedirect('/tasks');
        $this->get('/tasks/'.$task->id.'/edit')->assertNotFound();
        $this->put('/tasks/'.$task->id, $this->taskData(['status' => 'done']))->assertNotFound();
        Mail::assertNothingSent();
    }

    public function test_invalid_update_does_not_change_task_or_generate_email(): void
    {
        $this->login();
        Mail::fake();
        $task = Task::create($this->taskData());
        $this->put('/tasks/'.$task->id, $this->taskData(['title' => '', 'status' => 'done']))
            ->assertSessionHasErrors('title');
        $this->assertSame('pending', $task->fresh()->status);
        Mail::assertNothingSent();
    }

    public function test_log_transport_writes_a_real_rendered_email_once(): void
    {
        $this->login();
        $logPath = storage_path('framework/testing/completion-'.Str::uuid().'.log');
        config([
            'mail.default' => 'log',
            'mail.mailers.log.channel' => 'completion_test',
            'logging.channels.completion_test' => ['driver' => 'single', 'path' => $logPath, 'level' => 'debug'],
            'tasks.completion_recipient' => 'log-proof@example.com',
        ]);
        Mail::purge('log');
        $task = Task::create($this->taskData(['title' => 'Mail proof task']));
        try {
            for ($save = 0; $save < 2; $save++) {
                $this->put('/tasks/'.$task->id, $this->taskData(['title' => 'Mail proof task', 'status' => 'done']))
                    ->assertRedirect('/tasks')->assertSessionHasNoErrors();
            }
            $content = File::get($logPath);
            $this->assertSame(1, substr_count($content, 'Subject: Task completed: Mail proof task'));
            $this->assertStringContainsString('log-proof@example.com', $content);
            $this->assertStringContainsString('A task has been completed', $content);
        } finally {
            \Illuminate\Support\Facades\Log::forgetChannel('completion_test');
            if (File::exists($logPath)) { File::delete($logPath); }
        }
    }

    public function test_mail_transport_failure_rolls_back_the_task_update(): void
    {
        $this->login();
        $task = Task::create($this->taskData());
        Mail::shouldReceive('to')->once()->andReturnSelf();
        Mail::shouldReceive('send')->once()->andThrow(new TransportException('Simulated mail transport failure'));
        $this->from('/tasks/'.$task->id.'/edit')->put('/tasks/'.$task->id, $this->taskData(['status' => 'done']))
            ->assertRedirect('/tasks/'.$task->id.'/edit')->assertSessionHasErrors('notification');
        $this->assertSame('pending', $task->fresh()->status);
    }

    public function test_public_lists_exclude_archived_records_but_show_active_done_tasks(): void
    {
        $done = Task::create($this->taskData(['title' => 'Visible done task', 'status' => 'done']));
        $archived = Task::create($this->taskData(['title' => 'Hidden archived task']));
        $archived->forceFill(['is_archived' => true])->save();
        foreach (['/', '/tasks'] as $url) {
            $this->get($url)->assertOk()->assertSee($done->title)->assertDontSee($archived->title);
        }
    }

    public function test_registration_stores_a_hashed_password_and_enables_management(): void
    {
        $this->post('/register', [
            'username' => 'registered.user', 'full_name' => 'Registered User',
            'email' => 'registered@example.com', 'password' => 'private-test-password',
            'password_confirmation' => 'private-test-password',
        ])->assertRedirect('/dashboard')->assertSessionHasNoErrors();
        $user = User::firstOrFail();
        $this->assertTrue(\Illuminate\Support\Facades\Hash::check('private-test-password', $user->password));
        $this->assertArrayNotHasKey('password', $user->toArray());
        $this->get('/tasks/create')->assertOk();
        $this->post('/logout')->assertRedirect('/');
        $this->get('/tasks/create')->assertRedirect('/login');
        $this->get('/tasks')->assertOk();
    }
}
