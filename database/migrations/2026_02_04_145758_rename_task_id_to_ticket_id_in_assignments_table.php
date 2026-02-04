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
    Schema::table('task_assignments', function (Blueprint $table) {
        // تغییر نام ستون از task_id به ticket_id
        if (Schema::hasColumn('task_assignments', 'task_id')) {
            $table->renameColumn('task_id', 'ticket_id');
        }
    });
}

public function down(): void
{
    Schema::table('task_assignments', function (Blueprint $table) {
        if (Schema::hasColumn('task_assignments', 'ticket_id')) {
            $table->renameColumn('ticket_id', 'task_id');
        }
    });
}
};
