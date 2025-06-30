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
        // 1. Remove duplicate children table if it exists
        if (Schema::hasTable('children')) {
            Schema::dropIfExists('children');
        }

        // Recreate children table with correct structure
        Schema::create('children', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->date('date_of_birth');
            $table->enum('gender', ['male', 'female', 'other']);
            $table->text('notes')->nullable();
            $table->string('current_profile_photo_path', 2048)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // 2. Add Telegram chat_id to users table
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'telegram_chat_id')) {
                $table->string('telegram_chat_id')->nullable()->after('role');
            }
            
            // Ensure role enum matches requirements
            $table->enum('role', ['guardian', 'doctor', 'admin'])->default('guardian')->change();
        });

        // 3. Update insulin_types table
        Schema::table('insulin_types', function (Blueprint $table) {
            $table->boolean('rapid_flag')->after('name');
            
            // Migrate data from is_rapid to rapid_flag
            if (Schema::hasColumn('insulin_types', 'is_rapid')) {
                DB::statement('UPDATE insulin_types SET rapid_flag = is_rapid');
                $table->dropColumn('is_rapid');
            }
            
            // Remove unnecessary columns
            $columnsToDrop = ['description', 'sort_order', 'is_active'];
            foreach ($columnsToDrop as $column) {
                if (Schema::hasColumn('insulin_types', $column)) {
                    $table->dropColumn($column);
                }
            }
        });

        // 4. Update meals table
        Schema::table('meals', function (Blueprint $table) {
            // Add unique constraint
            $table->unique(['child_id', DB::raw('DATE(meal_time)'), 'meal_type'], 'unique_meal_per_type_per_day');
            
            // Rename columns to match requirements
            $table->renameColumn('pre_meal_bg', 'pre_bg');
            $table->renameColumn('post_meal_bg', 'post_bg');
            $table->renameColumn('food_description', 'food_desc');
            
            // Add missing columns
            if (!Schema::hasColumn('meals', 'status')) {
                $table->enum('status', ['open', 'closed'])->default('open')->after('sugars_grams');
            }
            
            if (!Schema::hasColumn('meals', 'override_flag')) {
                $table->boolean('override_flag')->default(false)->after('status');
            }
        });

        // 5. Update random_checks table
        Schema::table('random_checks', function (Blueprint $table) {
            $table->renameColumn('blood_glucose', 'bg_value');
            
            // Remove unnecessary columns
            $columnsToDrop = ['insulin_type_id', 'insulin_units', 'insulin_injected_at', 'is_high_alert', 'is_low_alert'];
            foreach ($columnsToDrop as $column) {
                if (Schema::hasColumn('random_checks', $column)) {
                    $table->dropColumn($column);
                }
            }
        });

        // 6. Update basal_doses table
        Schema::table('basal_doses', function (Blueprint $table) {
            // Add unique constraint
            $table->unique(['child_id', DB::raw('DATE(injected_at)')], 'one_basal_dose_per_day');
            
            // Rename injected_at to dose_date and change type to date
            if (Schema::hasColumn('basal_doses', 'injected_at')) {
                $table->date('dose_date')->nullable()->after('injected_at');
                DB::statement('UPDATE basal_doses SET dose_date = DATE(injected_at)');
                $table->dropColumn('injected_at');
                $table->renameColumn('dose_date', 'injected_at');
            }
            
            // Remove unnecessary columns
            $columnsToDrop = ['is_manual_entry', 'is_correction_dose', 'injection_site'];
            foreach ($columnsToDrop as $column) {
                if (Schema::hasColumn('basal_doses', $column)) {
                    $table->dropColumn($column);
                }
            }
        });

        // 7. Update notes table
        Schema::table('notes', function (Blueprint $table) {
            // Rename content to note_text to match requirements
            $table->renameColumn('content', 'note_text');
            
            // Remove unnecessary columns
            $columnsToDrop = ['title', 'type', 'is_important', 'tags', 'related_data'];
            foreach ($columnsToDrop as $column) {
                if (Schema::hasColumn('notes', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverse all changes made in the up method
        
        // 1. Revert notes table
        Schema::table('notes', function (Blueprint $table) {
            if (Schema::hasColumn('notes', 'note_text')) {
                $table->renameColumn('note_text', 'content');
            }
            
            // Note: We don't restore the dropped columns as we can't know their original state
        });
        
        // 2. Revert basal_doses table
        Schema::table('basal_doses', function (Blueprint $table) {
            if (Schema::hasColumn('basal_doses', 'injected_at')) {
                $table->timestamp('injected_at')->nullable(false)->change();
            }
            
            // Note: We don't restore the dropped columns as we can't know their original state
            // The unique constraint will be dropped automatically when the table is dropped
        });
        
        // 3. Revert random_checks table
        // Note: We don't restore the dropped columns as we can't know their original state
        
        // 4. Revert meals table
        Schema::table('meals', function (Blueprint $table) {
            if (Schema::hasColumn('meals', 'pre_bg')) {
                $table->renameColumn('pre_bg', 'pre_meal_bg');
            }
            
            if (Schema::hasColumn('meals', 'post_bg')) {
                $table->renameColumn('post_bg', 'post_meal_bg');
            }
            
            if (Schema::hasColumn('meals', 'food_desc')) {
                $table->renameColumn('food_desc', 'food_description');
            }
            
            // Note: We don't drop the added columns as we can't know if they existed before
        });
        
        // 5. Revert insulin_types table
        Schema::table('insulin_types', function (Blueprint $table) {
            if (Schema::hasColumn('insulin_types', 'rapid_flag')) {
                $table->boolean('is_rapid')->after('name');
                DB::statement('UPDATE insulin_types SET is_rapid = rapid_flag');
                $table->dropColumn('rapid_flag');
            }
        });
        
        // 6. Revert users table
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'telegram_chat_id')) {
                $table->dropColumn('telegram_chat_id');
            }
        });
        
        // 7. Drop the new children table if it exists
        Schema::dropIfExists('children');
    }
};
