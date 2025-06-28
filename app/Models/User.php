<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone_number',
        'address',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the contact books owned by this user
     */
    public function ownedContactBooks(): HasMany
    {
        return $this->hasMany(ContactBook::class, 'owner_id');
    }

    /**
     * Get the contact books this user has access to (not owned)
     */
    public function accessibleContactBooks()
    {
        return ContactBook::whereHas('userAccess', function ($query) {
            $query->where('email', $this->email);
        })->with('owner');
    }

    /**
     * Get all contact books this user can access (owned + shared)
     */
    public function allAccessibleContactBooks()
    {
        return ContactBook::where(function ($query) {
            $query->where('owner_id', $this->id)
                  ->orWhereHas('userAccess', function ($subQuery) {
                      $subQuery->where('email', $this->email);
                  });
        })->with('owner');
    }

    /**
     * Get user access records for contact books owned by this user
     */
    public function grantedAccess(): HasMany
    {
        return $this->hasMany(UserAccess::class, 'owner_id');
    }

    /**
     * Get user access records where this user has been granted access
     */
    public function receivedAccess(): HasMany
    {
        return $this->hasMany(UserAccess::class, 'email', 'email');
    }

    /**
     * Get user's contact book order preferences
     */
    public function contactBookOrders(): HasMany
    {
        return $this->hasMany(UserContactBookOrder::class);
    }

    /**
     * Get all contact books this user can access (owned + shared) with custom ordering
     */
    public function allAccessibleContactBooksOrdered()
    {
        // Ensure user has a personal contact book first
        $personalBook = $this->getOrCreatePersonalContactBook();

        $contactBooks = $this->allAccessibleContactBooks()->get();

        // Get user's custom ordering
        $orders = $this->contactBookOrders()
            ->pluck('sort_order', 'contact_book_id')
            ->toArray();

        // Separate personal and other contact books
        $foundPersonalBook = null;
        $otherBooks = collect();

        foreach ($contactBooks as $book) {
            if ($this->isPersonalContactBook($book)) {
                $foundPersonalBook = $book;
            } else {
                $otherBooks->push($book);
            }
        }

        // Use the personal book we ensured exists
        if (!$foundPersonalBook) {
            $foundPersonalBook = $personalBook;
        }

        // Sort other books by user's custom order, then by name
        $otherBooks = $otherBooks->sort(function ($a, $b) use ($orders) {
            $orderA = $orders[$a->id] ?? 9999; // Default high value for unordered
            $orderB = $orders[$b->id] ?? 9999;

            if ($orderA === $orderB) {
                return strcmp($a->name, $b->name); // Alphabetical fallback
            }

            return $orderA <=> $orderB;
        });

        // Personal book always first, then ordered books
        $result = collect();
        if ($foundPersonalBook) {
            $result->push($foundPersonalBook);
        }

        return $result->concat($otherBooks);
    }

    /**
     * Check if a contact book is the user's personal contact book
     */
    public function isPersonalContactBook(ContactBook $contactBook): bool
    {
        if ($contactBook->owner_id !== $this->id) {
            return false;
        }

        // Get the actual personal contact book
        $personalBook = $this->personalContactBook();

        // If we found a personal book by name patterns, compare by ID
        if ($personalBook) {
            return $contactBook->id === $personalBook->id;
        }

        // Fallback: if no personal book found by name patterns,
        // consider the oldest owned contact book as personal
        $oldestBook = $this->ownedContactBooks()->oldest()->first();
        return $oldestBook && $contactBook->id === $oldestBook->id;
    }

    /**
     * Update contact book order for this user
     */
    public function updateContactBookOrder(array $contactBookIds): void
    {
        // Remove existing orders for books not in the new list
        $this->contactBookOrders()
            ->whereNotIn('contact_book_id', $contactBookIds)
            ->delete();

        // Update or create orders for each contact book
        foreach ($contactBookIds as $index => $contactBookId) {
            $this->contactBookOrders()->updateOrCreate(
                ['contact_book_id' => $contactBookId],
                ['sort_order' => $index]
            );
        }
    }

    /**
     * Get the user's personal contact book
     */
    public function personalContactBook()
    {
        $firstName = explode(' ', $this->name)[0];
        $expectedName = $firstName . ' Contact Book';

        return $this->ownedContactBooks()
                    ->where(function($query) use ($expectedName) {
                        $query->where('name', $expectedName)
                              ->orWhere('name', 'Personal Dashboard'); // Legacy support
                    })
                    ->first();
    }

    /**
     * Get or create the user's personal contact book
     */
    public function getOrCreatePersonalContactBook(): ContactBook
    {
        $personal = $this->personalContactBook();

        if (!$personal) {
            // Get the user's first name
            $firstName = explode(' ', $this->name)[0];
            $defaultName = $firstName . ' Contact Book';

            $personal = $this->ownedContactBooks()->create([
                'name' => $defaultName,
                'color' => 'blue',
            ]);
        }

        return $personal;
    }

    /**
     * Check if user can access a specific contact book
     */
    public function canAccessContactBook(ContactBook $contactBook): bool
    {
        return $contactBook->canUserView($this);
    }

    /**
     * Check if user can edit a specific contact book
     */
    public function canEditContactBook(ContactBook $contactBook): bool
    {
        return $contactBook->canUserEdit($this);
    }

    /**
     * Get the phone attribute (alias for phone_number)
     */
    public function getPhoneAttribute()
    {
        return $this->phone_number;
    }

    /**
     * Set the phone attribute (alias for phone_number)
     */
    public function setPhoneAttribute($value)
    {
        $this->attributes['phone_number'] = $value;
    }
}
