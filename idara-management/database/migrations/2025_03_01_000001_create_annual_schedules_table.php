<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ratiba ya Mwaka (Annual Schedule) - Awamu ya 3, prd.md §5.3 na
     * architecture.md §2.7. Hii ndiyo "mpango" wa shughuli za idara kwa
     * mwaka; `activity_logs` (angalia migration inayofuata) ndiyo "kilichofanyika
     * halisi" dhidi ya kila kipengele hapa - tofauti hii ndiyo msingi wa
     * dashibodi ya maendeleo (% ya ratiba iliyotekelezwa).
     */
    public function up(): void
    {
        Schema::create('annual_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('department_id')->constrained()->cascadeOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->unsignedSmallInteger('planned_year');
            $table->unsignedTinyInteger('planned_month'); // 1-12
            $table->enum('status', ['pending', 'completed', 'skipped'])->default('pending');
            $table->timestamps();

            $table->index(['department_id', 'planned_year', 'planned_month']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('annual_schedules');
    }
};
