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
        Schema::create('loans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->constrained()->restrictOnDelete();
            $table->foreignId('profile_id')->constrained()->restrictOnDelete();

            $table->date('loaned_at');
            $table->date('due_at')->nullable();           // date de retour prévue
            $table->date('returned_at')->nullable();      // null = encore emprunté

            $table->string('borrower_name')->nullable();  // si prêté hors famille
            $table->text('note')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loans');
    }
};
