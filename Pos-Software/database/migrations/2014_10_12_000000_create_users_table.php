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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150);
            $table->string('email', 150)->unique()->index();
            $table->string('phone', 20)->nullable()->index();
            $table->string('photo')->nullable();
            $table->text('address')->nullable();
            $table->unsignedBigInteger('role_id')->default(2); // Default role set to "user"
            $table->timestamp('email_verified_at')->nullable();
            $table->unsignedBigInteger('branch_id')->index();
            $table->string('password', 255);
            $table->rememberToken();
            $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('cascade')->onUpdate('cascade');
            $table->softDeletes(); // Added for soft delete functionality
            $table->timestamps(0); // Removed microsecond precision
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
