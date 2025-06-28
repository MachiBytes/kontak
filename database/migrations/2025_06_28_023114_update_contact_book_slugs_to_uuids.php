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
        // Update all existing contact books to have UUID slugs
        $contactBooks = ContactBook::all();
        foreach ($contactBooks as $contactBook) {
            $contactBook->update(['slug' => (string) Str::uuid()]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Regenerate name-based slugs for existing contact books
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
    }
};
