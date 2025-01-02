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
        Schema::create('withdraws', function (Blueprint $table) {
            $table->id();
            $table->decimal('amount', 12, 2);
            $table->unsignedBigInteger('transaction_id')->unsigned();
            $table->foreign('transaction_id')->references('transaction_id')->on('transactions');
            $table->unsignedBigInteger('bank_id')->unsigned();
            $table->foreign('bank_id')->references('id')->on('banks');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('withdraws');
    }
};