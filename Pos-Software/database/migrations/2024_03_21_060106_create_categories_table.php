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
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150)->index(); // Name field indexed with adjusted length
            $table->string('slug', 100)->unique(); // Reduced slug length
            $table->enum('status', ['active', 'inactive'])->default('active'); // Used enum for status
            $table->softDeletes(); // Added soft deletes
            $table->timestamps(0); // Removed microsecond precision
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
