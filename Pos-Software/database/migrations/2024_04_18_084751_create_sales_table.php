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
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('branch_id')->unsigned();
            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('cascade');
            $table->unsignedBigInteger('customer_id')->unsigned();
            $table->foreign('customer_id')->references('id')->on('customers');
            $table->date('sale_date')->nullable();
            $table->unsignedBigInteger('sale_by')->nullable();
            $table->foreign('sale_by')->references('id')->on('users');
            $table->string('invoice_number')->nullable();
            $table->enum('order_type', ['general', 'online'])->default('general');
            $table->decimal('delivery_charge')->nullable();
            $table->integer('total_quantity')->default(0); //total product quantity
            $table->decimal('total_price', 12, 2)->default(0); //total product price
            $table->string('discount')->nullable(); //user input
            $table->decimal('grand_total', 12, 2)->nullable();
            $table->enum('status', ['Draft', 'Completed', 'Returned']);
            $table->enum('payment_status', ['Paid', 'Partial_Paid', 'Processing', 'Due']);
            $table->string('note')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};
