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
        Schema::create('firearms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('profile_id')->nullable()->constrained('profiles')->onDelete('set null');
            $table->string('type');
            $table->string('make');
            $table->string('model');
            $table->string('caliber');
            $table->string('serial_no')->unique();
            $table->timestamps();
            $table->index(['serial_no']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('firearms');
    }
};
