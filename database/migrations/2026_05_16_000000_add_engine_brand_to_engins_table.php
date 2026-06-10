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
        Schema::table('engins', function (Blueprint $table) {
            $table->string('engine_brand')->nullable()->after('engine_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('engins', function (Blueprint $table) {
            $table->dropColumn('engine_brand');
        });
    }
};
