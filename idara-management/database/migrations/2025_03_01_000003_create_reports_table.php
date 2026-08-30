<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ripoti za PDF zilizozalishwa (na `report:generate` command au kwa
     * ombi la moja kwa moja la kiongozi) - angalia architecture.md §2.7.
     */
    public function up(): void
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('department_id')->constrained()->cascadeOnDelete();
            $table->foreignId('generated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('period'); // mfano: "2026" au "2026-Q1"
            $table->string('file_path');
            $table->timestamp('generated_at');
            $table->timestamps();

            $table->unique(['department_id', 'period']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
