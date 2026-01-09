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
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

            $table->date('attendance_date');
            $table->dateTime('check_in')->nullable();
            $table->dateTime('check_out')->nullable();

            $table->decimal('longitude', 10, 7)->nullable();
            $table->decimal('latitude', 10, 7)->nullable();

            $table->enum('status', ['on_time', 'late', 'absent', 'leave'])->default('on_time');

            $table->decimal('work_hours', 5, 2)->nullable();
            $table->string('absent_note')->nullable();
            $table->string('note')->nullable();

            $table->timestamps();
            $table->unique(['user_id', 'attendance_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
