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
        Schema::create('classrooms', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('category')->nullable();
            $table->integer('max_students')->default(0);
            $table->string('thumbnail_path')->nullable();

            $table->string('secret_code', 8)->unique()->nullable();
            $table->string('invite_code')->unique()->nullable();

            $table->foreignId('teacher_id')
                ->constrained('users')
                ->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('contentable', function (Blueprint $table) {
            $table->id();
            $table->foreignId('classroom_id')
                ->constrained('classrooms')
                ->onDelete('cascade');
            $table->morphs('contentable'); // This will create contentable_id and contentable_type columns
            $table->integer('order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('classrooms');
        Schema::dropIfExists('contentable');
    }
};
