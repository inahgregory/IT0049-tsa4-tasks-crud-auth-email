<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->boolean('is_archived')->default(false)->index();
        });
        DB::table('tasks')->where('status', 'completed')->update(['status' => 'done']);
    }
    public function down(): void
    {
        DB::table('tasks')->where('status', 'done')->update(['status' => 'completed']);
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropColumn('is_archived');
        });
    }
};
