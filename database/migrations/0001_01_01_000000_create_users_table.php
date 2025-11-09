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
        Schema::create('users', callback: function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('nom', 50)->index();
            $table->string('prenom', 70)->index();
            $table->string('email')->unique()->index();
            $table->string('telephone', 80)->unique()->index();
            $table->string('adresse', 150)->index();  
            $table->string('nci', 60)->unique()->index();
            $table->string('password');
            $table->string('is_verified')->default('false');
            $table->string('code_verification', 6)->nullable();
             $table->string('password_temporaire')->nullable();
            $table->softDeletes();
            $table->timestamps();
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

