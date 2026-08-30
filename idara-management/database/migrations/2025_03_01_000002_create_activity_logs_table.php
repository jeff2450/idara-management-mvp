<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Rekodi ya kile "kilichofanyika halisi" - kinaweza kuunganishwa na
     * kipengele cha ratiba (`annual_schedule_id`) au kiwe huru (shughuli
     * ya ghafla isiyopangwa). Angalia architecture.md §2.7 na prd.md §4.6.
     */
    public function up(): void
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('department_id')->constrained()->cascadeOnDelete();
            $table->foreignId('annual_schedule_id')->nullable()->constrained('annual_schedules')->nullOnDelete();
            $table->foreignId('recorded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->date('occurred_at');
            $table->timestamps();

            $table->index(['department_id', 'occurred_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
