<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Contact extends Model
{
    use HasFactory;

    protected $fillable = [
        'contact_book_id',
        'name',
        'phones',
        'emails',
        'websites',
        'address',
        'notes',
    ];

    protected $casts = [
        'phones' => 'array',
        'emails' => 'array',
        'websites' => 'array',
    ];

    /**
     * Get the contact book this contact belongs to
     */
    public function contactBook(): BelongsTo
    {
        return $this->belongsTo(ContactBook::class);
    }

    /**
     * Get the initials for this contact
     */
    public function getInitialsAttribute(): string
    {
        $words = explode(' ', $this->name);
        $initials = '';

        foreach ($words as $word) {
            if (!empty($word)) {
                $initials .= strtoupper(substr($word, 0, 1));
                if (strlen($initials) >= 2) break;
            }
        }

        return $initials ?: strtoupper(substr($this->name, 0, 2));
    }

    /**
     * Get the primary email address (first email in the list)
     */
    public function getPrimaryEmailAttribute(): ?string
    {
        $emails = $this->emails ?? [];
        return !empty($emails[0]['address']) ? $emails[0]['address'] : null;
    }

    /**
     * Get the primary phone number (first phone in the list)
     */
    public function getPrimaryPhoneAttribute(): ?string
    {
        $phones = $this->phones ?? [];
        return !empty($phones[0]['number']) ? $phones[0]['number'] : null;
    }

    /**
     * Get the primary website (first website in the list)
     */
    public function getPrimaryWebsiteAttribute(): ?string
    {
        $websites = $this->websites ?? [];
        return !empty($websites[0]['url']) ? $websites[0]['url'] : null;
    }

    /**
     * Get all emails as a formatted string for display
     */
    public function getFormattedEmailsAttribute(): string
    {
        $emails = $this->emails ?? [];
        if (empty($emails)) return '';

        return collect($emails)->map(function ($email) {
            $type = ucfirst($email['type'] ?? 'Email');
            return "{$type}: {$email['address']}";
        })->join(', ');
    }

    /**
     * Get all phones as a formatted string for display
     */
    public function getFormattedPhonesAttribute(): string
    {
        $phones = $this->phones ?? [];
        if (empty($phones)) return '';

        return collect($phones)->map(function ($phone) {
            $type = ucfirst($phone['type'] ?? 'Phone');
            return "{$type}: {$phone['number']}";
        })->join(', ');
    }

    /**
     * Get all websites as a formatted string for display
     */
    public function getFormattedWebsitesAttribute(): string
    {
        $websites = $this->websites ?? [];
        if (empty($websites)) return '';

        return collect($websites)->map(function ($website) {
            $type = ucfirst($website['type'] ?? 'Website');
            return "{$type}: {$website['url']}";
        })->join(', ');
    }

    /**
     * Add a phone number
     */
    public function addPhone(string $type, string $number): void
    {
        $phones = $this->phones ?? [];
        $phones[] = ['type' => $type, 'number' => $number];
        $this->phones = $phones;
    }

    /**
     * Add an email address
     */
    public function addEmail(string $type, string $address): void
    {
        $emails = $this->emails ?? [];
        $emails[] = ['type' => $type, 'address' => $address];
        $this->emails = $emails;
    }

    /**
     * Add a website
     */
    public function addWebsite(string $type, string $url): void
    {
        $websites = $this->websites ?? [];

        // Ensure URL has protocol
        if (!preg_match('/^https?:\/\//', $url)) {
            $url = 'https://' . $url;
        }

        $websites[] = ['type' => $type, 'url' => $url];
        $this->websites = $websites;
    }

    /**
     * Get searchable text for this contact
     */
    public function getSearchableTextAttribute(): string
    {
        $searchableText = $this->name;

        // Add emails
        if ($this->emails) {
            foreach ($this->emails as $email) {
                $searchableText .= ' ' . ($email['address'] ?? '');
            }
        }

        // Add phones
        if ($this->phones) {
            foreach ($this->phones as $phone) {
                $searchableText .= ' ' . ($phone['number'] ?? '');
            }
        }

        // Add websites
        if ($this->websites) {
            foreach ($this->websites as $website) {
                $searchableText .= ' ' . ($website['url'] ?? '');
            }
        }

        return $searchableText;
    }
}
