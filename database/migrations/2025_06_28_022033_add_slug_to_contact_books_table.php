<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use App\Models\ContactBook;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('contact_books', function (Blueprint $table) {
            $table->string('slug')->nullable()->after('name');
            $table->index('slug');
        });

        // Populate slugs for existing contact books
        $contactBooks = ContactBook::all();
        foreach ($contactBooks as $contactBook) {
            $baseSlug = Str::slug($contactBook->name);
            $slug = $baseSlug;
            $counter = 1;

            // Ensure unique slug
            while (ContactBook::where('slug', $slug)->where('id', '!=', $contactBook->id)->exists()) {
                $slug = $baseSlug . '-' . $counter;
                $counter++;
            }

            $contactBook->update(['slug' => $slug]);
        }

        // Make slug required after populating existing records
        Schema::table('contact_books', function (Blueprint $table) {
            $table->string('slug')->nullable(false)->unique()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contact_books', function (Blueprint $table) {
            $table->dropIndex(['slug']);
            $table->dropColumn('slug');
        });
    }
};
