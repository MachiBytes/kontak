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
            // Add the new contact_book_id column
            $table->foreignId('contact_book_id')->nullable()->constrained('contact_books')->onDelete('cascade');

            // Add index for the new column
            $table->index(['contact_book_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_access', function (Blueprint $table) {
            $table->dropForeign(['contact_book_id']);
            $table->dropIndex(['contact_book_id']);
            $table->dropColumn('contact_book_id');
        });
    }
};
