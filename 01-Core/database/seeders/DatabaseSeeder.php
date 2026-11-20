<?php
namespace Database\Seeders;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Seeder;
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Relative dates keep the first setup useful on any demonstration day.
        $today = today();
        $samples = [
            ['title' => 'Review Laravel migration notes', 'status' => 'done', 'day' => -1],
            ['title' => 'Outline the task management pages', 'status' => 'done', 'day' => -1],
            ['title' => 'Check the task database records', 'status' => 'done', 'day' => 0],
            ['title' => 'Practice Eloquent date filtering', 'status' => 'in_progress', 'day' => 0],
            ['title' => 'Capture application screenshots', 'status' => 'pending', 'day' => 0],
            ['title' => 'Review the GitHub submission files', 'status' => 'pending', 'day' => 0],
            ['title' => 'Deploy the Laravel application', 'status' => 'pending', 'day' => 1],
            ['title' => 'Verify the hosted submission link', 'status' => 'pending', 'day' => 1],
        ];
        foreach ($samples as $index => $sample) {
            Task::updateOrCreate(['id' => $index + 1], [
                'title' => $sample['title'],
                'status' => $sample['status'],
                'task_date' => $today->copy()->addDays($sample['day'])->toDateString(),
            ]);
        }

        $profile = User::firstOrNew(['id' => 1]);
        $profile->fill([
            'username' => 'inahgregorio',
            'full_name' => 'Inah Angela Gregorio',
            'email' => 'inah.gregorio@example.com',
        ]);
        if (! $profile->exists) { $profile->password = \Illuminate\Support\Str::password(40); }
        $profile->save();
    }
}

