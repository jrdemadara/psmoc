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
        Schema::create('gunclub_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('profile_id')->constrained('profiles')->onDelete('cascade');
            $table->foreignId('gunclub_id')->constrained('gunclubs')->onDelete('cascade');
            $table->boolean('is_main')->default(false);
            $table->string('years_no')->nullable();
            $table->timestamps();
            $table->unique(['profile_id', 'gunclub_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gunclub_members_migration');
    }
};
