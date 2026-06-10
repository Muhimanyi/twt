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
        Schema::create('certificats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('engin_id')->constrained()->onDelete('cascade');
            $table->string('type'); // Identification, Possession, Technique, etc.
            $table->string('certificate_number')->unique();
            $table->date('issued_at')->nullable();
            $table->date('expires_at')->nullable();
            $table->string('owner_name')->nullable();
            $table->string('status')->default('Actif');
            $table->string('qr_code')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('certificats');
    }
};
