<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Kumbukumbu za miamala/risiti za idara - angalia architecture.md §2.6.
     * HII NI DATA NYETI (fedha) - angalia TransactionPolicy: hata mwanachama
     * wa idara husika HAWEZI kuiona, ni Kiongozi/Admin pekee (architecture.md
     * §5 "ulinzi wa ziada wa access" kwa miamala ya fedha).
     */
    public function up(): void
    {
        Schema::create('department_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('department_id')->constrained()->cascadeOnDelete();
            $table->string('type');
            $table->decimal('amount', 12, 2);
            $table->text('description')->nullable();
            $table->foreignId('recorded_by')->constrained('users')->cascadeOnDelete();
            $table->date('occurred_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('department_transactions');
    }
};
