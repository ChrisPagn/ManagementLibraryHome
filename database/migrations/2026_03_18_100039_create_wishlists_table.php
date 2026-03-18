<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     */
    public function up(): void
    {
        Schema::create('wishlists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('profile_id')->constrained()->cascadeOnDelete();
            $table->foreignId('item_type_id')->nullable()->constrained()->nullOnDelete();

            $table->string('title');
            $table->string('author')->nullable();
            $table->string('isbn')->nullable();
            $table->text('note')->nullable();
            $table->enum('priority', ['low', 'medium', 'high'])->default('medium');
            $table->decimal('estimated_price', 8, 2)->nullable();
            $table->boolean('is_acquired')->default(false);
            $table->date('acquired_at')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wishlists');
    }
};
