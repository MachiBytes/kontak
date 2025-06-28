<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserAccess extends Model
{
    use HasFactory;

    protected $table = 'user_access';

    protected $fillable = [
        'contact_book_id',
        'owner_id', // Keep for backward compatibility during transition
        'email',
        'role',
        'invited_at',
        'last_accessed_at',
    ];

    protected $casts = [
        'invited_at' => 'datetime',
        'last_accessed_at' => 'datetime',
    ];

    /**
     * Get the contact book this access belongs to
     */
    public function contactBook(): BelongsTo
    {
        return $this->belongsTo(ContactBook::class);
    }

    /**
     * Get the user if they exist in the system
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'email', 'email');
    }

    /**
     * Get the owner (for backward compatibility)
     * This will be deprecated once we fully migrate to contact books
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /**
     * Check if this user has admin access
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if this user has audience (view-only) access
     */
    public function isAudience(): bool
    {
        return $this->role === 'audience';
    }

    /**
     * Update last accessed timestamp
     */
    public function updateLastAccessed(): void
    {
        $this->update(['last_accessed_at' => now()]);
    }
}
