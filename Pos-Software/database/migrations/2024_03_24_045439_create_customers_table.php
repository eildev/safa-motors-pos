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
            $table->string('business_name', 150)->nullable();
            $table->string('phone', 20)->index(); // Indexed for performance
            $table->text('address')->nullable();
<<<<<<< HEAD
            $table->enum('customer_type', ['Transport_owner', 'Technician','Floating'])->comment('Customer Type')->index();
=======
            $table->enum('customer_type', ['Transport_owner', 'Technician', 'Floating'])->comment('Customer Type')->index();
>>>>>>> 9e19399de37ea5d26385a2c6110a884ab35b57db
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
