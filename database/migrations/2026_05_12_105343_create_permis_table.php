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
        Schema::create('permis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conducteur_id')->constrained()->onDelete('cascade');
            $table->string('license_number')->unique();
            $table->string('category')->nullable();
            $table->date('issued_at')->nullable();
            $table->date('expires_at')->nullable();
            $table->string('status')->default('Valide'); // Valide, Expire, Suspendu
            $table->text('restrictions')->nullable();
            $table->string('qr_code')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permis');
    }
};
