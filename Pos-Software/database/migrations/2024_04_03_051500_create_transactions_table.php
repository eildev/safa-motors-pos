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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('branch_id')->unsigned();
            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('cascade');
            $table->unsignedBigInteger('transaction_ac')->unsigned();
            $table->foreign('transaction_ac')->references('id')->on('banks');
            $table->unsignedBigInteger('transaction_by')->nullable();
            $table->foreign('transaction_by')->references('id')->on('users');
            $table->integer('transaction_id')->unique();
            $table->decimal('amount', 12, 2)->nullable();
            $table->enum('purpose', ['Sell', 'Purchase', 'Expense', 'Return', 'Salary', 'Balance Deposit', 'Balance Withdraw', 'Other'])->comment('Recieve or Pay');
            $table->enum('transaction_type', ['In', 'Out']);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
