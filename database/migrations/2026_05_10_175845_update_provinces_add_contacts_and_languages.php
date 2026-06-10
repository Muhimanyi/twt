<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('provinces', function (Blueprint $table) {
            $table->dropColumn(['contacts', 'languages']);

            $table->string('contacts_email')->nullable();
            $table->string('contacts_phone')->nullable();

            $table->boolean('language_lingala')->default(false);
            $table->boolean('language_kikongo')->default(false);
            $table->boolean('language_kiluba')->default(false);
            $table->boolean('language_kiswahili')->default(false);
        });
    }

    public function down(): void
    {
        Schema::table('provinces', function (Blueprint $table) {
            $table->dropColumn([
                'contacts_email',
                'contacts_phone',
                'language_lingala',
                'language_kikongo',
                'language_kiluba',
                'language_kiswahili',
            ]);

            $table->text('contacts')->nullable();
            $table->text('languages')->nullable();
        });
    }
};
