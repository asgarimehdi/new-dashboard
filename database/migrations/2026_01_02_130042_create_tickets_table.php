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
       Schema::create('tickets', function (Blueprint $table) {
    $table->id();
    $table->string('ticket_code')->unique(); // کد پیگیری مثل T-1024
    $table->foreignId('user_id')->constrained(); // ثبت کننده تیکت (مبدأ)
    $table->foreignId('unit_id')->constrained(); // واحد مقصد (تاسیسات، IT و...)
    $table->string('subject'); // موضوع درخواست
    $table->text('content'); // متن اصلی درخواست
    $table->enum('priority', ['low', 'normal', 'urgent'])->default('normal');
    $table->enum('status', ['open', 'pending', 'answered', 'converted_to_task', 'closed'])->default('open');
    $table->foreignId('task_id')->nullable()->constrained()->onDelete('set null'); // ارتباط با تسک (اگر تبدیل شد)
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
