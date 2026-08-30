<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * architecture.md §5: "Data ya Idara ya Watoto na miamala ya fedha — ziwe
     * na ukaguzi wa ziada wa access". Badala ya kuhardcode jina "Idara ya
     * Watoto" popote kwenye code (idara ni configurable - §2.2), Admin
     * anaweka bendera hii kwenye idara yoyote inayohitaji ulinzi wa ziada.
     * Angalia Policies\DepartmentPolicy::manageMembers().
     */
    public function up(): void
    {
        Schema::table('departments', function (Blueprint $table) {
            $table->boolean('is_sensitive')->default(false)->after('description');
        });
    }

    public function down(): void
    {
        Schema::table('departments', function (Blueprint $table) {
            $table->dropColumn('is_sensitive');
        });
    }
};
