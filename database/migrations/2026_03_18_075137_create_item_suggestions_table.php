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
        Schema::create('item_suggestions', function (Blueprint $table) {
        $table->id();
        $table->foreignId('profile_id')->constrained()->cascadeOnDelete();
        $table->foreignId('item_type_id')->nullable()->constrained()->nullOnDelete();
        $table->string('title');
        $table->string('author')->nullable();
        $table->string('isbn')->nullable();
        $table->text('note')->nullable();  // Pourquoi tu veux cet item
        $table->enum('status', ['pending', 'approved', 'rejected'])
              ->default('pending');
        $table->text('admin_note')->nullable(); // Réponse de l'admin
        $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('item_suggestions');
    }
};
