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
        // ۱. حذف کلید خارجی قدیمی
        // نام این کلید طبق متن خطای شما: task_assignments_task_id_foreign
        $table->dropForeign('task_assignments_task_id_foreign');

        // ۲. ایجاد کلید خارجی جدید متصل به جدول تیکت‌ها
        $table->foreign('ticket_id')
              ->references('id')
              ->on('tickets')
              ->onDelete('cascade');
    });
}

public function down(): void
{
    Schema::table('task_assignments', function (Blueprint $table) {
        $table->dropForeign(['ticket_id']);
        $table->foreign('ticket_id')->references('id')->on('tasks');
    });
}
};
