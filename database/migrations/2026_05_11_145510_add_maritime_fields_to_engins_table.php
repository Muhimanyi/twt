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
            // Maritime specific fields
            $table->string('vessel_name')->nullable()->after('vehicle_color'); // Dénomination de l'engin
            $table->string('registration_number')->nullable()->after('vessel_name'); // Numéro d'immatriculation
            $table->string('registration_place')->nullable()->after('registration_number'); // Lieu d'immatriculation
            $table->date('registration_date')->nullable()->after('registration_place'); // Date d'immatriculation
            $table->string('capacity')->nullable()->after('registration_date'); // Capacité d'embarcation
            $table->string('navigation_line')->nullable()->after('capacity'); // Ligne de navigation
            $table->float('height')->nullable()->after('navigation_line'); // Hauteur
            $table->float('length')->nullable()->after('height'); // Longueur
            $table->float('width')->nullable()->after('length'); // Largeur
            $table->string('propulsion_type')->nullable()->after('width'); // Type de propulsion (si pas de moteur)
            $table->json('usage')->nullable()->after('propulsion_type'); // Usage (personel, marchandise, peche, privee, autres)

            // Crew Info
            $table->integer('crew_count')->nullable()->after('usage'); // Nombre d'equipage/pecheur
            $table->integer('driver_count')->nullable()->after('crew_count'); // Nombre de conducteurs
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('engins', function (Blueprint $table) {
            $table->dropColumn([
                'vessel_name',
                'registration_number',
                'registration_place',
                'registration_date',
                'capacity',
                'navigation_line',
                'height',
                'length',
                'width',
                'propulsion_type',
                'usage',
                'crew_count',
                'driver_count',
            ]);
        });
    }
};
