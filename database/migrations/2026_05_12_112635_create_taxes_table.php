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
        Schema::create('taxes', function (Blueprint $table) {
            $table->id();
            $table->string('label');
            $table->string('beneficiary');
            $table->string('target'); // Conducteur, Engin, Transporteur, Passager, Chargeur
            $table->decimal('amount', 15, 2)->default(0);
            $table->string('currency')->default('USD');
            $table->string('frequency')->default('Annuelle'); // Mensuelle, Trimestrielle, Semestrielle, Annuelle, Unique
            $table->string('sector')->nullable(); // routier, lacustre, aerien, ferroviaire
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('taxes');
    }
};
