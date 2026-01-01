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
       Schema::create('tasks', function (Blueprint $table) {
    $table->id();

    $table->string('title');
    $table->text('description')->nullable();

    // واحدی که مشکل مربوط به آن است
    $table->foreignId('unit_id')
        ->constrained()
        ->cascadeOnDelete();

    // کاربر ثبت‌کننده
    $table->foreignId('created_by')
        ->nullable()
        ->constrained('users')
        ->nullOnDelete();

    $table->foreignId('task_status_id')
        ->constrained()
        ->restrictOnDelete();
$table->string('priority')->default('normal'); // low, normal, urgent
    $table->timestamp('due_date')->nullable(); // مهلت انجام
    $table->string('attachment_path')->nullable(); // مسیر فایل پیوست
    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
