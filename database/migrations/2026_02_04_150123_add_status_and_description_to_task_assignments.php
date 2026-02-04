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
        // اضافه کردن ستون وضعیت ارجاع
        if (!Schema::hasColumn('task_assignments', 'status')) {
            $table->string('status')->default('pending')->after('to_user_id'); 
            // pending | accepted | completed
        }

        // اضافه کردن توضیحات ارجاع (مثلاً مدیر می‌نویسد: لطفا سریع بررسی شود)
        if (!Schema::hasColumn('task_assignments', 'description')) {
            $table->text('description')->nullable()->after('status');
        }
    });
}

public function down(): void
{
    Schema::table('task_assignments', function (Blueprint $table) {
        $table->dropColumn(['status', 'description']);
    });
}
};
