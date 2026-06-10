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
        Schema::create('paiements', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();
            $table->foreignId('taxe_id')->constrained()->cascadeOnDelete();
            $table->morphs('payable'); // payable_type, payable_id pour Conducteur, Engin, etc.
            $table->decimal('amount', 15, 2);
            $table->string('currency')->default('USD');
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('province_id')->nullable()->constrained()->nullOnDelete();
            $table->string('payment_method')->default('cash'); // cash, bank, mobile_money
            $table->text('notes')->nullable();
            $table->boolean('is_printed')->default(false);
            $table->timestamp('paid_at')->useCurrent();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('paiements');
    }
};
