<?php

namespace App\Http\Controllers;

use App\Models\ContactBook;
use App\Models\UserAccess;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // Get the current contact book
        $contactBook = $this->getCurrentContactBook($request);

        // Check if user can access this contact book
        if (!$user->canAccessContactBook($contactBook)) {
            abort(403, 'You do not have access to this contact book.');
        }

        // Track last accessed timestamp for shared contact books
        $this->updateLastAccessedTimestamp($contactBook, $user);

        // Get contacts with search functionality
        $search = $request->get('search');
        $contacts = $contactBook->contacts()
            ->when($search, function ($query, $search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");

                    // Search in JSON fields
                    $q->orWhereRaw("JSON_SEARCH(phones, 'one', ?) IS NOT NULL", ["%{$search}%"])
                      ->orWhereRaw("JSON_SEARCH(emails, 'one', ?) IS NOT NULL", ["%{$search}%"])
                      ->orWhereRaw("JSON_SEARCH(websites, 'one', ?) IS NOT NULL", ["%{$search}%"]);
                });
            })
            ->orderBy('name')
            ->get();

        // Group contacts by first letter
        $groupedContacts = $contacts->groupBy(function ($contact) {
            return strtoupper(substr($contact->name, 0, 1));
        })->sortKeys();

        // Check if user can edit contacts
        $canEdit = $this->canUserEditContacts($contactBook, $user);

        return view('home', compact('contactBook', 'groupedContacts', 'search', 'canEdit'));
    }

    /**
     * Get the current contact book based on the request
     */
    private function getCurrentContactBook(Request $request): ContactBook
    {
        $user = Auth::user();

        if ($dashboardSlug = $request->get('dashboard')) {
            $contactBook = $this->getContactBookBySlug($dashboardSlug);

            if ($contactBook && $user->canAccessContactBook($contactBook)) {
                return $contactBook;
            }
        }

        // Default to personal contact book
        return $user->getOrCreatePersonalContactBook();
    }

    /**
     * Get or create a contact book by slug
     */
    private function getContactBookBySlug(string $slug): ?ContactBook
    {
        return ContactBook::findBySlug($slug);
    }

    /**
     * Update last accessed timestamp for shared contact books
     */
    private function updateLastAccessedTimestamp(ContactBook $contactBook, User $user): void
    {
        // Only track for shared contact books (not personal ones)
        if ($contactBook->owner_id !== $user->id) {
            $userAccess = $contactBook->getUserAccess($user->email);
            if ($userAccess) {
                $userAccess->updateLastAccessed();
            }
        }
    }

    /**
     * Check if the given user can edit contacts in the given contact book
     */
    private function canUserEditContacts(ContactBook $contactBook, $user): bool
    {
        // Owner can always edit
        if ($contactBook->owner_id === $user->id) {
            return true;
        }

        // Check if user has admin access
        $userAccess = $contactBook->getUserAccess($user->email);
        return $userAccess && $userAccess->isAdmin();
    }
}
