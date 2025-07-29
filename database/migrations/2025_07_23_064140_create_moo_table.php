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
        Schema::create('moo', function (Blueprint $table) {
            $table->id();
            $table->foreignId('profile_id')->nullable()->constrained('profiles')->onDelete('cascade');
            $table->integer('exame_score');
            $table->string('zone');
            $table->string('district');
            $table->enum('result', ['passed', 'failed', 'complete']);
            $table->string('recommended_by')->nullable();
            $table->string('approved_by')->nullable();
            $table->date('expiry_date');
            $table->enum('status', ['pending', 'approved', 'recommended', 'denied'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('moo');
    }
};
