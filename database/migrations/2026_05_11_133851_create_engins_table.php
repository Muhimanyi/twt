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
        Schema::create('engins', function (Blueprint $table) {
            $table->id();
            $table->string('identification_number')->unique();
            $table->string('category'); // roulant, flottant
            $table->string('sub_category'); // avec moteur, sans moteur

            // Owner Info
            $table->string('owner_name');
            $table->string('owner_gender');
            $table->string('owner_birth_place');
            $table->string('owner_father_name');
            $table->string('owner_mother_name');
            $table->string('owner_marital_status');
            $table->string('owner_nationality');
            $table->string('owner_phone');
            $table->string('owner_email')->nullable();
            $table->text('owner_address');

            // Vehicle Info (Common)
            $table->string('vehicle_genre')->nullable();
            $table->string('vehicle_brand')->nullable();
            $table->string('vehicle_type')->nullable();
            $table->string('vehicle_color')->nullable();
            $table->string('plate_number')->nullable();

            // Motorized Specific Info
            $table->string('engine_number')->nullable();
            $table->string('chassis_number')->nullable();
            $table->integer('manufacture_year')->nullable();
            $table->integer('horsepower')->nullable();

            // Admin Info
            $table->string('identification_place');
            $table->date('identification_date');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('engins');
    }
};
