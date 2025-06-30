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
        Schema::table('insulin_types', function (Blueprint $table) {
            $table->string('onset')->nullable()->after('description');
            $table->string('peak')->nullable()->after('onset');
            $table->string('duration')->nullable()->after('peak');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('insulin_types', function (Blueprint $table) {
            $table->dropColumn(['onset', 'peak', 'duration']);
        });
    }
};
