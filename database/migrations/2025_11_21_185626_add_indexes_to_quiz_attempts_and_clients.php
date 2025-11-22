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
        Schema::table('quiz_attempts', function (Blueprint $table) {
            // Index for ranking queries (ordering by score and date)
            $table->index(['score', 'created_at']);
            // Index for user lookups if not already present (polymorphic)
            $table->index(['user_id', 'user_type']);
        });

        Schema::table('clients', function (Blueprint $table) {
            // Index for ranking queries (ordering by referral points)
            $table->index('referral_points');
        });
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
