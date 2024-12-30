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
        Schema::create('brands', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150)->index(); // Adjusted length and indexed
            $table->string('slug', 100)->unique(); // Adjusted length
            $table->enum('status', ['active', 'inactive'])->default('active')->index(); // Used enum for status
            $table->softDeletes(); // Added soft delete functionality
            $table->timestamps(0); // Removed microsecond precision
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('brands');
    }
};
