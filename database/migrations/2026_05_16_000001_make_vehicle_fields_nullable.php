<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('engins', function (Blueprint $table) {
            $table->string('vehicle_genre')->nullable()->change();
            $table->string('vehicle_brand')->nullable()->change();
            $table->string('vehicle_type')->nullable()->change();
            $table->string('vehicle_color')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('engins', function (Blueprint $table) {
            $table->string('vehicle_genre')->change();
            $table->string('vehicle_brand')->change();
            $table->string('vehicle_type')->change();
            $table->string('vehicle_color')->change();
        });
    }
};
