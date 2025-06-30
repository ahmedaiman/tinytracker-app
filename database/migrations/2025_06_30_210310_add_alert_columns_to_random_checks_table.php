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
        Schema::table('random_checks', function (Blueprint $table) {
            $table->boolean('is_high_alert')->default(false)->after('notes');
            $table->boolean('is_low_alert')->default(false)->after('is_high_alert');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('random_checks', function (Blueprint $table) {
            $table->dropColumn(['is_high_alert', 'is_low_alert']);
        });
    }
};
