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
        Schema::create('sub_categories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('category_id')->index(); // Indexed for better performance
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
            $table->string('name', 150)->index(); // Adjusted length and indexed
            $table->string('slug', 100)->unique(); // Adjusted length
            $table->enum('status', ['active', 'inactive'])->default('active'); // Used enum for clarity
            $table->softDeletes(); // Added for soft delete functionality
            $table->timestamps(0); // Removed microsecond precision
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sub_categories');
    }
};
