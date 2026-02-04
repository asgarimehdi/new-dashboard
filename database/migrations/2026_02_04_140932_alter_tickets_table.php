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
    Schema::table('tickets', function (Blueprint $table) {
        // فیلد پرچم برای تشخیص تسک بودن
        $table->boolean('is_task')->default(false)->after('status');
        
        // وضعیت اجرایی تسک (اتصال به جدول وضعیت‌هایی که قبلاً داشتی)
        $table->foreignId('task_status_id')->nullable()->after('is_task')
              ->constrained('task_statuses')->nullOnDelete();
        
        // زمان‌بندی‌ها
        $table->timestamp('accepted_at')->nullable()->after('updated_at');
        $table->timestamp('completed_at')->nullable()->after('accepted_at');
        $table->date('due_date')->nullable()->after('completed_at');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
{
    Schema::table('tickets', function (Blueprint $table) {
        $table->dropForeign(['task_status_id']);
        $table->dropColumn(['is_task', 'task_status_id', 'accepted_at', 'completed_at', 'due_date']);
    });
}
};
