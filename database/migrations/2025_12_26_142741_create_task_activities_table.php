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
        Schema::create('task_activities', function (Blueprint $table) {
    $table->id();

    $table->foreignId('task_id')
        ->constrained()
        ->cascadeOnDelete();

    $table->foreignId('user_id')
        ->nullable()
        ->constrained('users')
        ->nullOnDelete();

    $table->string('action'); 
    // assign | status_change | comment | return

    $table->text('description')->nullable();

    $table->foreignId('old_status_id')
        ->nullable()
        ->constrained('task_statuses')
        ->nullOnDelete();

    $table->foreignId('new_status_id')
        ->nullable()
        ->constrained('task_statuses')
        ->nullOnDelete();

    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('task_activities');
    }
};
