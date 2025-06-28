<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserContactBookOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'contact_book_id',
        'sort_order',
    ];

    /**
     * Get the user that owns this order preference
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the contact book this order preference is for
     */
    public function contactBook(): BelongsTo
    {
        return $this->belongsTo(ContactBook::class);
    }
}
