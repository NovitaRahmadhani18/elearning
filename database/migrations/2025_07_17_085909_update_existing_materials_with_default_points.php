<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Material;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update existing materials with default points if they don't have any
        Material::whereNull('points')
            ->orWhere('points', 0)
            ->update(['points' => 10]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert points back to 0 for materials that had null/0 points
        Material::where('points', 10)->update(['points' => 0]);
    }
};
