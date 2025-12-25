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
      Schema::create('unit_type_hierarchies', function (Blueprint $table) {
    $table->id();

    $table->foreignId('parent_unit_type_id')
        ->constrained('unit_types')
        ->cascadeOnDelete();

    $table->foreignId('child_unit_type_id')
        ->constrained('unit_types')
        ->cascadeOnDelete();

    $table->unique(
        ['parent_unit_type_id', 'child_unit_type_id'],
        'uth_parent_child_unique'
    );

   
    $table->timestamps();
});


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('unit_type_hierarchies');
    }
};
