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
        Schema::table('user_access', function (Blueprint $table) {
            // Make owner_id nullable since we're transitioning to use contact_book_id
            $table->foreignId('owner_id')->nullable()->change();

            // Drop the unique constraint that includes owner_id
            $table->dropUnique(['owner_id', 'email']);

            // Add a new unique constraint for contact_book_id and email
            $table->unique(['contact_book_id', 'email']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_access', function (Blueprint $table) {
            // Remove the new unique constraint
            $table->dropUnique(['contact_book_id', 'email']);

            // Make owner_id required again
            $table->foreignId('owner_id')->nullable(false)->change();

            // Restore the original unique constraint
            $table->unique(['owner_id', 'email']);
        });
    }
};
