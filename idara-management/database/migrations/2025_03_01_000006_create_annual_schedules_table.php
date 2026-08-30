<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ratiba ya mwaka ya shughuli zilizopangwa - angalia architecture.md §2.7.
     * `planned_month` ni namba 1-12, kwa mwaka ambao haujabainishwa moja kwa
     * moja (ratiba inarudiwa kila mwaka) - angalia AnnualSchedule model kwa
     * uwezekano wa kuongeza `year` baadaye kama inahitajika.
     */
    public function up(): void
    {
        Schema::create('annual_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('department_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->unsignedTinyInteger('planned_month');
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('annual_schedules');
    }
};
