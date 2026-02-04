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
        // ۱. حذف کلید خارجی قدیمی (نام این کلید در خطای شما ذکر شده بود)
        $table->dropForeign('task_activities_task_id_foreign');

        // ۲. ایجاد کلید خارجی جدید که به جدول tickets اشاره می‌کند
        $table->foreign('ticket_id')
              ->references('id')
              ->on('tickets')
              ->onDelete('cascade');
    });
}

public function down(): void
{
    Schema::table('task_activities', function (Blueprint $table) {
        $table->dropForeign(['ticket_id']);
        // برگشت به وضعیت قبل (در صورت نیاز)
        $table->foreign('ticket_id')->references('id')->on('tasks');
    });
}
};
