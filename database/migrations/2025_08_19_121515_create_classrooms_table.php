<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('classrooms_categories', function (Blueprint $table) {
            $table->id();
            $table->string('value')->unique();
            $table->string('name');
        });

        DB::table('classrooms_categories')->insert([
            ['value' => '7A', 'name' => '7A'],
            ['value' => '7B', 'name' => '7B'],
            ['value' => '7C', 'name' => '7C'],
            ['value' => '8A', 'name' => '8A'],
            ['value' => '8B', 'name' => '8B'],
            ['value' => '8C', 'name' => '8C'],
            ['value' => '9A', 'name' => '9A'],
            ['value' => '9B', 'name' => '9B'],
            ['value' => '9C', 'name' => '9C'],
        ]);

        Schema::create('statuses', function (Blueprint $table) {
            $table->id();
            $table->string('value')->unique();
            $table->string('name');
        });

        DB::table('statuses')->insert([
            ['value' => 'active', 'name' => 'Active'],
            ['value' => 'inactive', 'name' => 'Inactive'],
            ['value' => 'archived', 'name' => 'Archived'],
        ]);


        Schema::create('classrooms', function (Blueprint $table) {

            $table->id();

            $table->foreignId('teacher_id')
                ->constrained('users')
                ->onDelete('cascade');

            $table->string('name');

            $table->string('code')->unique();

            $table->text('description')->nullable();

            $table->foreignId('category_id')
                ->constrained('classrooms_categories')
                ->onDelete('cascade');

            $table->foreignId('status_id')
                ->constrained('statuses')
                ->onDelete('cascade');

            $table->string('thumbnail')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('classrooms');
        Schema::dropIfExists('statuses');
        Schema::dropIfExists('classrooms_categories');
    }
};
