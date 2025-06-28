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
        Schema::create('user_contact_book_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('contact_book_id')->constrained('contact_books')->onDelete('cascade');
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            // Ensure a user can only have one order entry per contact book
            $table->unique(['user_id', 'contact_book_id']);

            // Index for efficient ordering queries
            $table->index(['user_id', 'sort_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_contact_book_orders');
    }
};
