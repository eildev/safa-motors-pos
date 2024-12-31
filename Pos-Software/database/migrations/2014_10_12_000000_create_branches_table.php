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
        Schema::create('branches', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100); // Branch name must be unique
            $table->string('slug', 100)->unique(); // Branch name must be unique
            $table->string('address', 250);
            $table->string('phone', 20)->index(); // Indexed for better query performance
            $table->string('email', 100)->nullable()->index(); // Increased length and added index
            $table->string('logo', 150)->nullable(); // Adjusted length for larger file paths
            $table->integer('manager_id')->nullable();
            $table->softDeletes(); // Added for soft delete functionality
            $table->timestamps(0); // Removed microsecond precision
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('branches');
    }
};
