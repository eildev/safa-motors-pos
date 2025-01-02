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
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->string('slug', 255)->unique(); // Updated
            $table->string('email', 255)->unique()->nullable();
            $table->string('business_name', 255)->nullable();
            $table->string('address', 255)->nullable();
            $table->string('phone', 20)->unique()->index(); // Indexed for performance
            $table->tinyInteger('supplier_type')->comment('1: Wholesale, 2: Retailer'); // Changed from enum
            $table->decimal('due_balance', 12, 2)->default(0); // UnsignedDecimal used
            $table->softDeletes(); // Added for soft delete functionality
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('suppliers');
    }
};
