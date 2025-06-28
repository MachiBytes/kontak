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
        Schema::create('user_access', function (Blueprint $table) {
            $table->id();
            $table->foreignId('owner_id')->constrained('users')->onDelete('cascade');
            $table->string('email');
            $table->enum('role', ['admin', 'audience'])->default('audience');
            $table->timestamp('last_accessed_at')->nullable();
            $table->timestamp('invited_at')->useCurrent();
            $table->timestamps();

            $table->unique(['owner_id', 'email']);
            $table->index(['owner_id', 'role']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_access');
    }
};
