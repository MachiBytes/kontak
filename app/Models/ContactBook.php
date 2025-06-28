<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class ContactBook extends Model
{
    use HasFactory;

    protected $fillable = [
        'owner_id',
        'name',
        'slug',
        'color',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($contactBook) {
            if (empty($contactBook->slug)) {
                $contactBook->slug = (string) Str::uuid();
            }
        });
    }

    /**
     * Generate a unique UUID slug for the contact book
     */
    public function generateUniqueSlug(): string
    {
        return (string) Str::uuid();
    }

    /**
     * Get the contact book by its slug
     */
    public static function findBySlug(string $slug): ?ContactBook
    {
        return static::where('slug', $slug)->first();
    }

    /**
     * Get the URL for this contact book
     */
    public function getUrlAttribute(): string
    {
        return route('home', ['dashboard' => $this->slug]);
    }

    /**
     * Get the owner of the contact book
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /**
     * Get all contacts in this contact book
     */
    public function contacts(): HasMany
    {
        return $this->hasMany(Contact::class);
    }

    /**
     * Get all user access records for this contact book
     */
    public function userAccess(): HasMany
    {
        return $this->hasMany(UserAccess::class);
    }

    /**
     * Check if a user has access to this contact book
     */
    public function hasUserAccess(string $email): bool
    {
        return $this->userAccess()->where('email', $email)->exists();
    }

    /**
     * Get user access for a specific email
     */
    public function getUserAccess(string $email): ?UserAccess
    {
        return $this->userAccess()->where('email', $email)->first();
    }

    /**
     * Check if a user can edit this contact book
     */
    public function canUserEdit(User $user): bool
    {
        return $this->owner_id === $user->id;
    }

    /**
     * Check if a user can view this contact book
     */
    public function canUserView(User $user): bool
    {
        // Owner can always view
        if ($this->owner_id === $user->id) {
            return true;
        }

        // Check if user has been granted access
        return $this->userAccess()->where('email', $user->email)->exists();
    }

    /**
     * Get users who have access to this contact book (excluding owner)
     */
    public function getAccessUsers()
    {
        return $this->userAccess()
            ->with('user')
            ->get()
            ->map(function ($access) {
                return [
                    'id' => $access->id,
                    'email' => $access->email,
                    'user' => $access->user,
                    'granted_at' => $access->created_at,
                ];
            });
    }

    /**
     * Grant access to a user by email
     */
    public function grantAccessToUser(string $email): UserAccess
    {
        return $this->userAccess()->create([
            'email' => $email,
        ]);
    }

    /**
     * Revoke access from a user
     */
    public function revokeAccessFromUser(string $email): bool
    {
        return $this->userAccess()->where('email', $email)->delete() > 0;
    }
}
