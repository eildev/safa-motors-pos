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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150);
            $table->string('slug', 255)->unique();
            $table->string('email', 255)->unique()->nullable();
            $table->string('business_name', 150)->nullable();
            $table->string('phone', 20)->unique()->index(); // Indexed for performance
            $table->text('address')->nullable();
            $table->enum('customer_type', ['Transport_owner', 'Technician', 'Floating'])->comment('Customer Type')->index();
            $table->decimal('due_balance', 12, 2)->default(0);
            $table->softDeletes(); // Added soft delete
            $table->timestamps(0); // Removed microsecond precision
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
