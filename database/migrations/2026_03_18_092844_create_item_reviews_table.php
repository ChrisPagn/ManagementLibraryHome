<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Cette migration crée la table `item_reviews` 
     * pour stocker les avis et les statuts de lecture 
     * des items par les profils.
     * Chaque avis est lié à un item et à un profil, 
     * avec des champs pour le statut de lecture, 
     * la note et le commentaire.
     * Un profil ne peut laisser qu'un seul avis 
     * par item grâce à une contrainte d'unicité.
     */
    public function up(): void
{
    Schema::create('item_reviews', function (Blueprint $table) {
        $table->id();
        $table->foreignId('item_id')->constrained()->cascadeOnDelete();
        $table->foreignId('profile_id')->constrained()->cascadeOnDelete();

        // Statut de lecture
        $table->enum('reading_status', [
            'to_read',      // À lire/jouer
            'in_progress',  // En cours
            'completed',    // Terminé
            'abandoned',    // Abandonné
        ])->nullable();

        // Avis
        $table->unsignedTinyInteger('rating')->nullable(); // 1 à 5
        $table->text('comment')->nullable();

        $table->timestamps();

        // Un seul avis par profil par item
        $table->unique(['item_id', 'profile_id']);
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('item_reviews');
    }
};
