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
        Schema::create('transaction_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('branch_id')->unsigned();
            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('cascade');
            $table->unsignedBigInteger('transaction_id')->unsigned();
            $table->foreign('transaction_id')->references('transaction_id')->on('transactions')->onDelete('cascade');
            $table->enum('transaction_type', ['In', 'Out']);
            $table->decimal('amount', 12, 2)->nullable();
            $table->unsignedBigInteger('sell_id')->unsigned()->nullable();
            $table->foreign('sell_id')->references('id')->on('sales');
            $table->unsignedBigInteger('purchase_id')->unsigned()->nullable();
            $table->foreign('purchase_id')->references('id')->on('purchases');
            $table->unsignedBigInteger('expense_id')->unsigned()->nullable();
            $table->foreign('expense_id')->references('id')->on('expenses');
            $table->unsignedBigInteger('return_id')->unsigned()->nullable();
            $table->foreign('return_id')->references('id')->on('returns');
            $table->unsignedBigInteger('return_id')->unsigned()->nullable();
            $table->foreign('return_id')->references('id')->on('returns');
            $table->unsignedBigInteger('salary_id')->unsigned()->nullable();
            $table->foreign('salary_id')->references('id')->on('employee_salaries');
            $table->unsignedBigInteger('deposit_id')->unsigned()->nullable();
            $table->foreign('deposit_id')->references('id')->on('deposits');
            $table->unsignedBigInteger('withdraw_id')->unsigned()->nullable();
            $table->foreign('withdraw_id')->references('id')->on('withdraws');
            $table->integer('others_id')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaction_details');
    }
};
