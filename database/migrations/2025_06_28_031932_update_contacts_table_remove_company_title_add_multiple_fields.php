<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('contacts', function (Blueprint $table) {
            // Remove company and title columns
            $table->dropColumn(['company', 'title']);

            // Add new JSON fields for multiple entries
            $table->json('phones')->nullable()->after('email');
            $table->json('emails')->nullable()->after('email');
            $table->json('websites')->nullable()->after('phones');
        });

        // Migrate existing single phone and email to the new JSON fields
        $contacts = DB::table('contacts')->get();

        foreach ($contacts as $contact) {
            $phones = [];
            $emails = [];

            // Migrate existing phone
            if (!empty($contact->phone)) {
                $phones[] = [
                    'type' => 'mobile',
                    'number' => $contact->phone
                ];
            }

            // Migrate existing email
            if (!empty($contact->email)) {
                $emails[] = [
                    'type' => 'personal',
                    'address' => $contact->email
                ];
            }

            DB::table('contacts')
                ->where('id', $contact->id)
                ->update([
                    'phones' => !empty($phones) ? json_encode($phones) : null,
                    'emails' => !empty($emails) ? json_encode($emails) : null,
                    'websites' => null
                ]);
        }

        // Remove the old single phone and email columns
        Schema::table('contacts', function (Blueprint $table) {
            $table->dropColumn(['phone', 'email']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contacts', function (Blueprint $table) {
            // Add back the old columns
            $table->string('phone')->nullable()->after('name');
            $table->string('email')->nullable()->after('name');
            $table->string('company')->nullable()->after('phone');
            $table->string('title')->nullable()->after('company');
        });

        // Migrate data back from JSON to single fields (taking first entry)
        $contacts = DB::table('contacts')->get();

        foreach ($contacts as $contact) {
            $phone = null;
            $email = null;

            // Get first phone from JSON
            if (!empty($contact->phones)) {
                $phones = json_decode($contact->phones, true);
                if (!empty($phones[0]['number'])) {
                    $phone = $phones[0]['number'];
                }
            }

            // Get first email from JSON
            if (!empty($contact->emails)) {
                $emails = json_decode($contact->emails, true);
                if (!empty($emails[0]['address'])) {
                    $email = $emails[0]['address'];
                }
            }

            DB::table('contacts')
                ->where('id', $contact->id)
                ->update([
                    'phone' => $phone,
                    'email' => $email
                ]);
        }

        // Remove the JSON columns
        Schema::table('contacts', function (Blueprint $table) {
            $table->dropColumn(['phones', 'emails', 'websites']);
        });
    }
};
