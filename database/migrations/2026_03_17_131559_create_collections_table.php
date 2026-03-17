<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * - item_type_id : référence à la table item_types (ex: "Manga", "BD", "Roman")
     */
    public function up(): void
    {
        Schema::create('collections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_type_id')->constrained()->restrictOnDelete();

            $table->string('name');                    // "Harry Potter", "Astérix"
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('author')->nullable();
            $table->integer('total_volumes')->nullable(); // Nb tomes connus (null = inconnu)
            $table->boolean('is_complete')->default(false); // Série terminée ?
            $table->string('cover')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('collections');
    }
};
