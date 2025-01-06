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
        Schema::create('purchases', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('branch_id')->unsigned();
            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('cascade');
            $table->unsignedBigInteger('supplier_id')->unsigned();
            $table->foreign('supplier_id')->references('id')->on('suppliers');
            $table->date('purchase_date');
            $table->unsignedBigInteger('purchase_by')->nullable();
            $table->foreign('purchase_by')->references('id')->on('users');
            $table->decimal('total_quantity', 12, 2);
            $table->decimal('total_price', 12, 2);
            $table->string('invoice_number')->nullable();
            $table->decimal('discount', 12, 2)->nullable();
            $table->decimal('grand_total', 12, 2);
            $table->enum('status', ['Purchased', 'Processing', 'Canceled']);
            $table->enum('payment_status', ['Paid', 'Partial_Paid', 'Processing', 'Due']);
            $table->string('note')->nullable();
            $table->string('file')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchases');
    }
};
