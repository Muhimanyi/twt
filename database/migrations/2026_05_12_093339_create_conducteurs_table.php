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
        Schema::create('conducteurs', function (Blueprint $table) {
            $table->id();
            $table->string('identification_number')->unique();
            $table->string('sector'); // routier, lacustre, aerien, ferroviaire
            $table->foreignId('engin_id')->nullable()->constrained('engins')->onDelete('set null');
            $table->foreignId('province_id')->constrained('provinces');

            // Identity
            $table->string('name');
            $table->string('gender');
            $table->string('birth_place');
            $table->date('birth_date');
            $table->string('father_name');
            $table->string('mother_name');
            $table->string('marital_status');
            $table->string('nationality');
            $table->string('profession');

            // License
            $table->string('license_number')->nullable();
            $table->string('license_category')->nullable();
            $table->date('license_issued_at')->nullable();
            $table->date('license_expires_at')->nullable();

            // ID Piece
            $table->string('id_piece_type');
            $table->string('id_piece_number');
            $table->string('id_piece_issued_place');
            $table->date('id_piece_issued_at');

            // Migratory (Foreigners)
            $table->string('migratory_document_type')->nullable();
            $table->string('visa_type')->nullable();

            // Contact
            $table->string('phone');
            $table->string('email')->nullable();
            $table->text('address');

            // Other
            $table->string('affiliation')->nullable(); // Assoc/Agence affiliée
            $table->string('transport_mode')->nullable(); // Diurne, Nocturne
            $table->date('expiration_date')->nullable(); // Date d'expiration de la carte d'ID
            $table->string('photo_path')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('conducteurs');
    }
};
