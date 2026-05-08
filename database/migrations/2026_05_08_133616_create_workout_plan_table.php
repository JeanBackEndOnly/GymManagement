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
        Schema::create('workout_plan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('workout_type_id')->constrained('workout_types')->onDelete('cascade');
            $table->date('workout_started')->nullable();
            $table->date('workout_finished')->nullable();
            $table->string('workout_status')->nullable();
            $table->string('workout_file')->nullable();
            $table->time('workout_starts')->nullable();
            $table->integer('workout_duration')->nullable();
            $table->time('workout_time')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workout_plan');
    }
};
