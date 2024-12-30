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
        Schema::create('pos_settings', function (Blueprint $table) {
            $table->id();
            $table->string('company');
            $table->string('logo')->nullable();
            $table->text('address')->nullable();
            $table->string('email')->nullable();
            $table->string('facebook')->nullable();
            $table->string('phone')->nullable();
            // $table->string('page_link')->nullable();
            // $table->string('website')->nullable();
            $table->string('header_text')->nullable();
            $table->string('footer_text')->nullable();
            // $table->boolean('sale_over_sotck')->default(0);
            $table->enum('invoice_type',['a4','pos']);
            $table->enum('invoice_logo_type', ['Logo', 'Name', 'Both'])->default('Logo');
            $table->enum('barcode_type',['single','a4']);
            $table->integer('low_stock')->default(10);
            $table->boolean('dark_mode')->default(0);
            $table->boolean('discount')->default(0);
            $table->boolean('tax')->default(0);
            $table->boolean('barcode')->default(0);
            $table->boolean('via_sale')->default(0);
            $table->boolean('selling_price_edit')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pos_settings');
    }
};
