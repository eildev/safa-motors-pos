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
        Schema::create('drop_shipping_invoices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('branch_id')->unsigned();
            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('cascade');
            $table->unsignedBigInteger('dropshipping_products_id')->nullable();
            $table->foreign('dropshipping_products_id')->references('id')->on('drop_shipping_products');
            $table->unsignedBigInteger('retailer_supplier_id')->nullable();
            $table->foreign('retailer_supplier_id')->references('id')->on('suppliers');
            $table->integer('dropshipping_invoice_number')->unique()->nullable();
            $table->decimal('total_cost_price', 12, 2)->nullable();
            $table->decimal('total_sell_price', 12, 2)->nullable();
            $table->string('quantity')->nullable();
            $table->enum('status', ['Paid', 'Due']);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('drop_shipping_invoices');
    }
};
