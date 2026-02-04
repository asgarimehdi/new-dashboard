<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
  public function up(): void
{
    Schema::table('task_activities', function (Blueprint $table) {
        // اضافه کردن ستون is_internal اگر وجود ندارد
        if (!Schema::hasColumn('task_activities', 'is_internal')) {
            $table->boolean('is_internal')->default(false)->after('new_status_id');
        }

        // تغییر نام task_id به ticket_id برای هماهنگی با معماری جدید
        if (Schema::hasColumn('task_activities', 'task_id')) {
            $table->renameColumn('task_id', 'ticket_id');
        }
    });
}

public function down(): void
{
    Schema::table('task_activities', function (Blueprint $table) {
        $table->dropColumn('is_internal');
        $table->renameColumn('ticket_id', 'task_id');
    });
}
};
