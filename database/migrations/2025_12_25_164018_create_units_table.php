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
        Schema::create('units', function (Blueprint $table) {
    $table->id();

    $table->string('name');

    $table->foreignId('unit_type_id')
        ->constrained()
        ->restrictOnDelete();

    $table->foreignId('parent_id')
        ->nullable()
        ->references('id')
        ->on('units')
        ->nullOnDelete();

    $table->foreignId('city_id')
         ->nullable();
        

    $table->boolean('is_active')->default(true);

    $table->timestamps();

    $table->index(['unit_type_id', 'parent_id']);
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('units');
    }
};
