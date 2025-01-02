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
        Schema::create('product_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id')->unsigned();
            $table->text('description')->nullable();
            $table->unsignedBigInteger('unit');
            $table->foreign('unit')->references('id')->on('units');
            $table->unsignedBigInteger('size');
            $table->foreign('size')->references('id')->on('sizes');
            $table->string('color', 50)->nullable();
            $table->string('model_no', 100)->nullable();
            $table->string('quality', 100)->nullable();
            $table->string('image', 255)->nullable();
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_details');
    }
};
