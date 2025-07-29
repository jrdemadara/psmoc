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
        Schema::create('profiles', function (Blueprint $table) {
            $table->id();
            $table->enum('reg_type', ['new', 'renewal']);
            $table->string('application_venue');
            $table->string('ltopf_no');
            $table->string('license_type');

            $table->string('last_name');
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('extension')->nullable();
            $table->string('email');
            $table->string('phone');
            $table->date('birth_date');
            $table->string('birth_place');
            $table->integer('age');
            $table->string('gender');
            $table->string('civil_status');
            $table->string('blood_type');

            $table->string('region');
            $table->string('province');
            $table->string('city_municipality');
            $table->string('barangay');
            $table->string('purok');
            $table->string('street');

            $table->string('occupation');
            $table->string('company_organization')->nullable();
            $table->string('position')->nullable();
            $table->string('office_business_address')->nullable();
            $table->string('office_landline')->nullable();
            $table->string('office_email')->nullable();


            $table->text('photo')->nullable();
            $table->text('signature')->nullable();

            $table->string('recommended_by')->nullable();
            $table->string('approved_by')->nullable();
            $table->boolean('licensed_shooter')->default(false);
            $table->date('expiry_date');
            $table->enum('status', ['pending', 'approved', 'recommended', 'denied'])->default('pending');
            $table->timestamps();
            $table->index(['last_name', 'first_name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profiles_migration');
    }
};
