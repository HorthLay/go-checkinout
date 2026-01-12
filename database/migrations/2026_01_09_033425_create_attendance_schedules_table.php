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
        Schema::create('attendance_schedules', function (Blueprint $table) {
          $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

            $table->integer('late_allowed_min')->default(10);
            $table->time('scheduled_check_in_morining')->default('7:30');
            $table->time('scheduled_check_out_morining')->default('11:30');
            $table->time('scheduled_check_in_afternoon')->default('14:00');
            $table->time('scheduled_check_out_afternoon')->default('17:30');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendance_schedules');
    }
};
