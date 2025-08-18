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
            $table->string('qrcode')->nullable();
            $table->string('temp_id')->nullable();
            $table->string('rfid')->nullable();
            $table->enum('reg_type', ['new', 'renewal'])->default('new');
            $table->string('application_venue')->nullable();
            $table->string('ltopf_no')->nullable();
            $table->string('license_type')->nullable();

            $table->string('last_name')->nullable();
            $table->string('first_name')->nullable();
            $table->string('middle_name')->nullable();
            $table->string('extension')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->date('birth_date')->nullable();
            $table->string('birth_place')->nullable();
            $table->integer('age')->nullable();
            $table->string('gender');
            $table->string('civil_status')->nullable();
            $table->string('blood_type')->nullable();

            $table->string('region')->nullable();
            $table->string('province')->nullable();
            $table->string('city_municipality')->nullable();
            $table->string('barangay')->nullable();
            $table->string('purok')->nullable();
            $table->string('street')->nullable();

            $table->string('occupation')->nullable();
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
            $table->enum('status', ['pending', 'on-hold', 'approved', 'recommended', 'denied'])->default('pending');
            $table->timestamps();
            $table->index(['last_name', 'first_name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profiles');
    }
};
