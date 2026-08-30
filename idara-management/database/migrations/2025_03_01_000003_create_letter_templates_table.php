<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Angalia architecture.md §2.5. Templates hizi ni za JUMLA (siyo za idara
     * moja pekee) - Admin anazitengeneza, kiongozi yeyote anaweza kuzitumia
     * kuzalisha barua kwa idara yake. Hii ndiyo sababu hakuna department_id
     * hapa (tofauti na `letters` chini).
     */
    public function up(): void
    {
        Schema::create('letter_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('body_template');
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('letter_templates');
    }
};
