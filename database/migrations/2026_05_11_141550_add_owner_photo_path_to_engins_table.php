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
            $table->string('owner_photo_path')->nullable()->after('owner_address');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('engins', function (Blueprint $table) {
            $table->dropColumn('owner_photo_path');
        });
    }
};
