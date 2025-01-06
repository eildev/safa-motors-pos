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
        Schema::create('units', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50)->unique(); // Added unique constraint for unit name
            $table->enum('status', ['active', 'inactive'])->default('active')->index(); // Used enum for status
            $table->softDeletes(); // Added for soft delete functionality
            $table->timestamps(0); // Removed microsecond precision
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('units');
    }
};
