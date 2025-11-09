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
        Schema::create('comptes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('numero_compte')->unique()->index();
            $table->uuid('user_id')->index(); 
            $table->string('titulaire', 100);
            $table->enum('type', ['epargne', 'cheque']);
            $table->string('devise', 10)->default('FCFA');
            $table->enum('statut', ['actif', 'bloque', 'ferme'])->default('actif');
            $table->text('motif_blocage')->nullable();
            $table->timestamp('date_blocage')->nullable();            
            $table->timestamp('derniere_modification')->nullable();

            $table->timestamp('code_expire_at')->nullable();
            $table->integer('version')->default(1);

            $table->boolean('archived')->default(false);
            $table->timestamp('date_fin_blocage')->nullable();
            $table->boolean('is_bloqued')->default(false)->after('blocage_fin');

            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->softDeletes();


        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comptes');
    }
};