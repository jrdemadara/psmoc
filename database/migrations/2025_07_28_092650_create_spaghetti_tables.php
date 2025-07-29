<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // UserTypes Table
        Schema::create('usertypes', function (Blueprint $table) {
            $table->id('usertypeserial');
            $table->string('typename');
            $table->text('description')->nullable();

            $table->timestamps();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->softDeletes();
            $table->unsignedBigInteger('deleted_by')->nullable();
        });

        // UserAccounts Table
        Schema::create('useraccounts', function (Blueprint $table) {
            $table->id('userserial');
            $table->string('username')->unique();
            $table->string('password');
            $table->string('fullname');
            $table->string('position')->nullable();
            $table->string('address')->nullable();
            $table->string('contactno')->nullable();
            $table->string('emailadd')->nullable();

            $table->unsignedBigInteger('usertype_serial');
            $table->foreign('usertype_serial')->references('usertypeserial')->on('usertypes')->onDelete('cascade');

            $table->timestamps();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->softDeletes();
            $table->unsignedBigInteger('deleted_by')->nullable();
        });

        // UserLogs Table
        Schema::create('userlogs', function (Blueprint $table) {
            $table->id('logserial');
            $table->string('logtable');
            $table->dateTime('logdate');
            $table->string('logactivity');
            $table->string('logmodule');

            $table->unsignedBigInteger('user_serial');
            $table->foreign('user_serial')->references('userserial')->on('useraccounts')->onDelete('cascade');

            $table->timestamps();
        });

        // Mmodules Table
        Schema::create('mmodules', function (Blueprint $table) {
            $table->id('modid');
            $table->string('modname');

            $table->unsignedBigInteger('parentid')->nullable();
            $table->foreign('parentid')->references('modid')->on('mmodules')->onDelete('cascade');

            $table->timestamps();
        });

        // Access Table
        Schema::create('accesses', function (Blueprint $table) {
            $table->id('accessserial');

            $table->unsignedBigInteger('usertype_serial');
            $table->unsignedBigInteger('modid');
            $table->boolean('hasaccess')->default(false);

            $table->foreign('usertype_serial')->references('usertypeserial')->on('usertypes')->onDelete('cascade');
            $table->foreign('modid')->references('modid')->on('mmodules')->onDelete('cascade');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accesses');
        Schema::dropIfExists('mmodules');
        Schema::dropIfExists('userlogs');
        Schema::dropIfExists('useraccounts');
        Schema::dropIfExists('usertypes');
    }
};
