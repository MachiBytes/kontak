<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\User;
use App\Models\ContactBook;
use App\Models\UserAccess;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // For each user that has granted access to others, create a personal contact book
        // and migrate their user access records to use that contact book

        $usersWithGrantedAccess = UserAccess::where('owner_id', '!=', null)
            ->select('owner_id')
            ->distinct()
            ->get();

        foreach ($usersWithGrantedAccess as $accessRecord) {
            $user = User::find($accessRecord->owner_id);

            if ($user) {
                // Get or create the user's personal contact book
                $contactBook = ContactBook::firstOrCreate([
                    'owner_id' => $user->id,
                    'name' => 'Personal Dashboard',
                ], [
                    'color' => 'blue',
                ]);

                // Update all user access records for this owner to use the contact book
                UserAccess::where('owner_id', $user->id)
                    ->whereNull('contact_book_id')
                    ->update(['contact_book_id' => $contactBook->id]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // This is a data migration, we don't reverse it
        // The contact books will remain, but the old owner_id field is still there
    }
};
