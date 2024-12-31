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
        Schema::create('company_balances', function (Blueprint $table) {
            $table->id();
            $table->decimal('deposit', 12, 2);
            $table->decimal('withdraw', 12, 2);
            $table->decimal('current_balance', 12, 2);
            $table->date('closing_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_balances');
    }
};
