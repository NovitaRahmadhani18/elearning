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
        Schema::table('content_users', function (Blueprint $table) {
            $table->integer('completion_time')->nullable()->after('updated_at')->comment('Time in seconds to complete the content');
            $table->decimal('points_earned', 8, 2)->nullable()->after('completion_time')->comment('Points earned from this content');
            $table->decimal('score', 5, 2)->nullable()->after('points_earned')->comment('Score for quiz content (null for materials)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('content_users', function (Blueprint $table) {
            $table->dropColumn(['completion_time', 'points_earned', 'score']);
        });
    }
};
