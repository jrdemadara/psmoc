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
        Schema::create('gunclubs', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('address');
            $table->string('contact_person');
            $table->string('contact_no');
            $table->string('email_address');
            $table->text('logo')->nullable();
            $table->string('approved_by')->nullable();
            $table->enum('status', ['pending', 'approved', 'recommended', 'denied'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gunclubs_migration');
    }
};
