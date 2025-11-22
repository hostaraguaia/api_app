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
        // Check and create index for score/created_at if it doesn't exist
        if (!Schema::hasIndex('quiz_attempts', 'quiz_attempts_score_created_at_index')) {
            Schema::table('quiz_attempts', function (Blueprint $table) {
                $table->index(['score', 'created_at']);
            });
        }

        // Check and create index for user_id/user_type if it doesn't exist
        if (!Schema::hasIndex('quiz_attempts', 'quiz_attempts_user_id_user_type_index')) {
            Schema::table('quiz_attempts', function (Blueprint $table) {
                $table->index(['user_id', 'user_type']);
            });
        }

        // Check and create index for referral_points if it doesn't exist
        if (!Schema::hasIndex('clients', 'clients_referral_points_index')) {
            Schema::table('clients', function (Blueprint $table) {
                $table->index('referral_points');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quiz_attempts', function (Blueprint $table) {
            $table->dropIndex(['score', 'created_at']);
            $table->dropIndex(['user_id', 'user_type']);
        });

        Schema::table('clients', function (Blueprint $table) {
            $table->dropIndex(['referral_points']);
        });
    }
};
