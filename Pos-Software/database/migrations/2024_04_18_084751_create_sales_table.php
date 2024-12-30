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
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');

            $table->date('sale_date')->nullable();
            $table->integer('sale_by')->nullable();
            $table->string('invoice_number')->nullable();
            $table->enum('order_type', ['general', 'online'])->default('general');
            $table->decimal('delivery_charge')->nullable();
            // $table->unsignedBigInteger('delivery_method_id')->nullable();
            // $table->unsignedBigInteger('area_id')->nullable();
            // $table->unsignedBigInteger('order_status')->nullable();
            // $table->unsignedBigInteger('agent_id')->nullable();
            // Initial Data -> Actual Pricing of the added products
            $table->integer('quantity')->default(0); //total product quantity
            $table->decimal('total', 12, 2)->default(0); //total product price
            $table->string('discount')->nullable(); //user input
            $table->decimal('change_amount', 12, 2)->nullable();
            // $table->string('delivery_cost')->nullable(); //user input
            $table->decimal('actual_discount', 12, 2)->default(0); //calculated discount
            $table->integer('tax')->nullable(); //calculated tax
            $table->decimal('receivable', 12, 2)->nullable(); //receivable after discount

            // Update on payment create/delete
            $table->decimal('paid', 12, 2)->default(0); //total paid

            // updates on new sell / return create/delete
            $table->decimal('returned', 12, 2)->default(0); //returned amount
            $table->decimal('final_receivable', 12, 2)->default(0); //after return -> receivable
            $table->decimal('due', 12, 2)->default(0); // updated due
            $table->decimal('total_purchase_cost', 12, 2)->nullable(); //updated after return
            $table->decimal('profit', 10, 2)->default(0);
            $table->integer('payment_method');
            $table->text('note')->nullable();
            // $table->integer('courier_id');
            // $table->integer('delivery_method_id');
            // $table->decimal('delivery_cost');
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
