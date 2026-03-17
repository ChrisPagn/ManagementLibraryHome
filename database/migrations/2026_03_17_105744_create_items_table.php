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
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_type_id')->constrained()->restrictOnDelete();

            $table->string('title');
            $table->string('subtitle')->nullable();
            $table->text('description')->nullable();
            $table->string('cover')->nullable();          // chemin image uploadée

            // Métadonnées communes
            $table->string('author')->nullable();
            $table->string('publisher')->nullable();
            $table->year('published_year')->nullable();
            $table->string('language', 10)->nullable();   // "fr", "en"...

            // ISBN (livres / BD)
            $table->string('isbn', 20)->nullable()->unique();

            // Informations supplémentaires flexibles
            $table->json('extra')->nullable(); // champs spécifiques par type

            // Statut
            $table->enum('status', ['available', 'borrowed', 'lost'])
                ->default('available');

            $table->timestamps();
            $table->softDeletes(); // Soft delete pour ne pas perdre l'historique
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
