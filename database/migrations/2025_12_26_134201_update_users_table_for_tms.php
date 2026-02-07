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
        Schema::table('users', function (Blueprint $table) {

    // نام کامل
    $table->string('full_name')->after('id');

    // کد ملی = نام کاربری
    $table->string('national_code', 10)->unique()->after('full_name');

    // اتصال به واحد
    $table->foreignId('unit_id')
        ->nullable()
        ->constrained('units')
        ->nullOnDelete()
        ->after('national_code');

    // وضعیت
    $table->boolean('is_active')->default(true)->after('unit_id');

    // فعلاً رمز عبور nullable
    // $table->string('password')->nullable()->change();
    $table->dropColumn('name');
     $table->dropUnique(['email']); // حذف unique index
    $table->dropColumn('email');
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
