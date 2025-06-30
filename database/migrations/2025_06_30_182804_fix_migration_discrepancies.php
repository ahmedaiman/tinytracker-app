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
        // First, disable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        // 2. Drop the children table if it exists
        if (Schema::hasTable('children')) {
            Schema::dropIfExists('children');
        }
        
        // Re-enable foreign key checks after dropping
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

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

        // 3. Ensure insulin_types table has is_rapid column
        if (Schema::hasTable('insulin_types')) {
            // Add is_rapid column if it doesn't exist
            if (!Schema::hasColumn('insulin_types', 'is_rapid')) {
                Schema::table('insulin_types', function (Blueprint $table) {
                    $table->boolean('is_rapid')->default(false)->after('description');
                });
            }
        }

        // 4. Update meals table
        if (Schema::hasTable('meals')) {
            // First rename columns if they exist
            if (Schema::hasColumn('meals', 'pre_meal_bg')) {
                Schema::table('meals', function (Blueprint $table) {
                    $table->renameColumn('pre_meal_bg', 'pre_bg');
                });
            }
            
            if (Schema::hasColumn('meals', 'post_meal_bg')) {
                Schema::table('meals', function (Blueprint $table) {
                    $table->renameColumn('post_meal_bg', 'post_bg');
                });
            }
            
            if (Schema::hasColumn('meals', 'food_description')) {
                Schema::table('meals', function (Blueprint $table) {
                    $table->renameColumn('food_description', 'food_desc');
                });
            }
            
            // Add missing columns
            Schema::table('meals', function (Blueprint $table) {
                if (!Schema::hasColumn('meals', 'status')) {
                    $table->enum('status', ['open', 'closed'])->default('open')->after('sugars_grams');
                }
                
                if (!Schema::hasColumn('meals', 'override_flag')) {
                    $table->boolean('override_flag')->default(false)->after('status');
                }
            });
            
            // Add a generated column for the date part of meal_time
            if (!Schema::hasColumn('meals', 'meal_date')) {
                DB::statement('ALTER TABLE meals ADD COLUMN meal_date DATE GENERATED ALWAYS AS (DATE(meal_time)) STORED');
                
                // Add unique constraint on the generated column
                Schema::table('meals', function (Blueprint $table) {
                    $table->unique(['child_id', 'meal_date', 'meal_type'], 'unique_meal_per_type_per_day');
                });
            }
        }

        // 5. Update random_checks table
        if (Schema::hasTable('random_checks')) {
            // Rename blood_glucose to bg_value if it exists
            if (Schema::hasColumn('random_checks', 'blood_glucose')) {
                Schema::table('random_checks', function (Blueprint $table) {
                    $table->renameColumn('blood_glucose', 'bg_value');
                });
            }
            
            // Drop foreign key constraint for insulin_type_id if it exists
            if (Schema::hasColumn('random_checks', 'insulin_type_id')) {
                Schema::table('random_checks', function (Blueprint $table) {
                    // Check if the foreign key exists using a raw query
                    $foreignKeyExists = DB::select(
                        "SELECT COUNT(*) as count FROM information_schema.TABLE_CONSTRAINTS 
                        WHERE CONSTRAINT_SCHEMA = DATABASE() 
                        AND TABLE_NAME = 'random_checks' 
                        AND CONSTRAINT_NAME = 'random_checks_insulin_type_id_foreign' 
                        AND CONSTRAINT_TYPE = 'FOREIGN KEY'"
                    );
                    
                    // Only try to drop the foreign key if it exists
                    if ($foreignKeyExists[0]->count > 0) {
                        $table->dropForeign('random_checks_insulin_type_id_foreign');
                    }
                    
                    // Now drop the column
                    $table->dropColumn('insulin_type_id');
                });
            }
            
            // Drop other unnecessary columns
            $columnsToDrop = ['insulin_units', 'insulin_injected_at', 'is_high_alert', 'is_low_alert'];
            foreach ($columnsToDrop as $column) {
                if (Schema::hasColumn('random_checks', $column)) {
                    Schema::table('random_checks', function (Blueprint $table) use ($column) {
                        $table->dropColumn($column);
                    });
                }
            }
        }

        // 6. Update basal_doses table
        if (Schema::hasTable('basal_doses')) {
            // First, add a new date column if it doesn't exist
            if (!Schema::hasColumn('basal_doses', 'injection_date')) {
                Schema::table('basal_doses', function (Blueprint $table) {
                    $table->date('injection_date')->nullable()->after('injected_at');
                });
                
                // Copy the date part from injected_at to injection_date
                DB::statement('UPDATE basal_doses SET injection_date = DATE(injected_at)');
            }
            
            // Now modify the table to handle the date column
            Schema::table('basal_doses', function (Blueprint $table) {
                // Add unique constraint on the new date column
                $table->unique(['child_id', 'injection_date'], 'one_basal_dose_per_day');
                
                // Remove the old timestamp column if it exists
                if (Schema::hasColumn('basal_doses', 'injected_at')) {
                    $table->dropColumn('injected_at');
                }
                
                // Remove unnecessary columns
                $columnsToDrop = ['is_manual_entry', 'is_correction_dose', 'injection_site'];
                foreach ($columnsToDrop as $column) {
                    if (Schema::hasColumn('basal_doses', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }

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
