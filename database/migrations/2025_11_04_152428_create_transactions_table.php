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
        Schema::create('transactions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('compte_id')->index();
            $table->enum('type', ['depot', 'retrait']);
            $table->decimal('montant', 15, 2);
            $table->enum('status', ['en_attente', 'validee', 'rejete'])->default('en_attente');
            $table->text('description')->nullable();
            $table->string('reference')->unique();
            $table->timestamps();

            $table->foreign('compte_id')->references('id')->on('comptes')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
