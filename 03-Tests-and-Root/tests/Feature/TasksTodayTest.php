<?php
namespace Tests\Feature;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class TasksTodayTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Carbon::setTestNow(Carbon::parse('2026-11-20 14:00:00', config('app.timezone')));
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    public function test_seeder_creates_eight_tasks_three_dates_and_exactly_one_user_on_repeat(): void
    {
        $this->seed();
        $this->seed();
        $this->assertDatabaseCount('tasks', 8);
        $this->assertDatabaseCount('users', 1);
        $this->assertSame(3, Task::distinct()->count('task_date'));
        $this->assertSame(4, Task::forDate(today())->count());
    }

    public function test_welcome_only_displays_today_including_completed_tasks(): void
    {
        $this->seed();
        $response = $this->get('/')->assertOk()->assertSee('4');
        foreach (Task::all() as $task) {
            if ($task->task_date->isToday()) {
                $response->assertSee($task->title);
            } else {
                $response->assertDontSee($task->title);
            }
        }
        $response->assertSee('Check the task database records')->assertSee('Done');
    }

    public function test_all_tasks_are_displayed_in_date_order(): void
    {
        $this->seed();
        $this->get('/tasks')->assertOk()
            ->assertSeeInOrder(Task::orderBy('task_date')->orderBy('id')->pluck('title')->all());
    }

    public function test_filter_follows_the_application_day_after_midnight(): void
    {
        $this->seed();
        Carbon::setTestNow(Carbon::parse('2026-11-21 00:01:00', config('app.timezone')));
        $this->get('/')->assertOk()->assertSee('Deploy the Laravel application')
            ->assertDontSee('Practice Eloquent date filtering');
    }

    public function test_profile_uses_the_database_record(): void
    {
        $this->seed();
        User::first()->update(['full_name' => 'Updated Profile Name']);
        $this->get('/profile')->assertOk()->assertSee('Updated Profile Name')
            ->assertSee('inahgregorio')->assertSee('inah.gregorio@example.com');
    }

    public function test_all_pages_have_shared_navigation_and_about_identifies_developer(): void
    {
        $this->seed();
        foreach (['/', '/tasks', '/profile', '/about'] as $url) {
            $response = $this->get($url)->assertOk();
            foreach (['welcome', 'tasks', 'profile', 'about'] as $route) {
                $response->assertSee('href="'.route($route).'"', false);
            }
        }
        $this->get('/about')->assertSee('Inah Angela Gregorio');
    }

    public function test_empty_database_and_no_tasks_today_have_clear_states(): void
    {
        $this->get('/')->assertOk()->assertSee('No tasks scheduled for today.');
        $this->get('/tasks')->assertOk()->assertSee('No tasks have been added yet.');
        $this->get('/profile')->assertOk()->assertSee('No profile available');
        Task::create(['title' => 'Future task', 'task_date' => today()->addDays(7)]);
        $this->get('/')->assertOk()->assertDontSee('Future task');
        $this->get('/tasks')->assertOk()->assertSee('Future task');
    }

    public function test_database_default_status_is_pending(): void
    {
        DB::table('tasks')->insert([
            'title' => 'Uses the database default',
            'task_date' => today()->toDateString(),
            'created_at' => now(), 'updated_at' => now(),
        ]);
        $this->assertDatabaseHas('tasks', ['title' => 'Uses the database default', 'status' => 'pending']);
    }

    public function test_username_uniqueness_is_enforced(): void
    {
        $this->seed();
        $this->expectException(QueryException::class);
        User::create(['username' => 'inahgregorio', 'full_name' => 'Duplicate', 'email' => 'other@example.com']);
    }

    public function test_task_titles_are_html_escaped(): void
    {
        Task::create(['title' => '<script>alert("x")</script>', 'task_date' => today()]);
        $this->get('/')->assertOk()->assertSee('&lt;script&gt;', false)->assertDontSee('<script>', false);
    }
}

