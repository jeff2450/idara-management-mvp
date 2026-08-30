<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Kile kilichofanyika dhidi ya ratiba - angalia architecture.md §2.7.
     * `schedule_id` ni nullable kwa sababu shughuli za dharura (zisizo kwenye
     * ratiba) bado zinapaswa kuwa na uwezo wa kurekodiwa.
     */
    public function up(): void
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('department_id')->constrained()->cascadeOnDelete();
            $table->foreignId('schedule_id')->nullable()->constrained('annual_schedules')->nullOnDelete();
            $table->text('description');
            $table->foreignId('recorded_by')->constrained('users')->cascadeOnDelete();
            $table->date('occurred_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
