<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\ContactBook;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ContactController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of contacts for the current contact book
     */
    public function index(Request $request)
    {
        $contactBook = $this->getCurrentContactBook($request);

        // Check if user can access this contact book
        if (!Auth::user()->canAccessContactBook($contactBook)) {
            abort(403, 'You do not have access to this contact book.');
        }

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

        return view('home', compact('contactBook', 'groupedContacts', 'search'));
    }

    /**
     * Show the form for creating a new contact
     */
    public function create(Request $request)
    {
        $contactBook = $this->getCurrentContactBook($request);

        // Check if user can edit this contact book
        if (!$this->canUserEditContacts($contactBook)) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'You do not have permission to add contacts to this contact book.'
                ], 403);
            }
            abort(403);
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'contact' => null, // For new contact
                'contactBook' => $contactBook
            ]);
        }

        return view('contacts.create', compact('contactBook'));
    }

    /**
     * Store a newly created contact
     */
    public function store(Request $request)
    {
        $contactBook = $this->getCurrentContactBook($request);

        // Check if user can edit this contact book
        if (!$this->canUserEditContacts($contactBook)) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'You do not have permission to add contacts to this contact book.'
                ], 403);
            }
            abort(403);
        }

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phones' => ['nullable', 'array'],
            'phones.*.type' => ['required_with:phones.*', 'string', 'max:50'],
            'phones.*.number' => ['required_with:phones.*', 'string', 'max:20'],
            'emails' => ['nullable', 'array'],
            'emails.*.type' => ['required_with:emails.*', 'string', 'max:50'],
            'emails.*.address' => ['required_with:emails.*', 'email', 'max:255'],
            'websites' => ['nullable', 'array'],
            'websites.*.type' => ['required_with:websites.*', 'string', 'max:50'],
            'websites.*.url' => ['required_with:websites.*', 'url', 'max:255'],
            'address' => ['nullable', 'string', 'max:500'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $contact = $contactBook->contacts()->create([
            'name' => $request->name,
            'phones' => $this->processPhones($request->phones),
            'emails' => $this->processEmails($request->emails),
            'websites' => $this->processWebsites($request->websites),
            'address' => $request->address,
            'notes' => $request->notes,
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Contact created successfully!',
                'contact' => $contact
            ]);
        }

        return redirect()->route('home', ['dashboard' => $contactBook->slug])
            ->with('success', 'Contact created successfully!');
    }

    /**
     * Display the specified contact
     */
    public function show(Request $request, Contact $contact)
    {
        $contactBook = $contact->contactBook;

        // Check if user can access this contact book
        if (!Auth::user()->canAccessContactBook($contactBook)) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'You do not have access to this contact.'
                ], 403);
            }
            abort(403);
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'contact' => $contact,
                'contactBook' => $contactBook,
                'canEdit' => $this->canUserEditContacts($contactBook)
            ]);
        }

        return view('contacts.show', compact('contact', 'contactBook'));
    }

    /**
     * Show the form for editing the specified contact
     */
    public function edit(Request $request, Contact $contact)
    {
        $contactBook = $contact->contactBook;

        // Check if user can edit this contact book
        if (!$this->canUserEditContacts($contactBook)) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'You do not have permission to edit this contact.'
                ], 403);
            }
            abort(403);
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'contact' => $contact,
                'contactBook' => $contactBook
            ]);
        }

        return view('contacts.edit', compact('contact', 'contactBook'));
    }

    /**
     * Update the specified contact
     */
    public function update(Request $request, Contact $contact)
    {
        $contactBook = $contact->contactBook;

        // Check if user can edit this contact book
        if (!$this->canUserEditContacts($contactBook)) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'You do not have permission to edit this contact.'
                ], 403);
            }
            abort(403);
        }

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phones' => ['nullable', 'array'],
            'phones.*.type' => ['required_with:phones.*', 'string', 'max:50'],
            'phones.*.number' => ['required_with:phones.*', 'string', 'max:20'],
            'emails' => ['nullable', 'array'],
            'emails.*.type' => ['required_with:emails.*', 'string', 'max:50'],
            'emails.*.address' => ['required_with:emails.*', 'email', 'max:255'],
            'websites' => ['nullable', 'array'],
            'websites.*.type' => ['required_with:websites.*', 'string', 'max:50'],
            'websites.*.url' => ['required_with:websites.*', 'url', 'max:255'],
            'address' => ['nullable', 'string', 'max:500'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $contact->update([
            'name' => $request->name,
            'phones' => $this->processPhones($request->phones),
            'emails' => $this->processEmails($request->emails),
            'websites' => $this->processWebsites($request->websites),
            'address' => $request->address,
            'notes' => $request->notes,
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Contact updated successfully!',
                'contact' => $contact->fresh()
            ]);
        }

        return redirect()->route('home', ['dashboard' => $contactBook->slug])
            ->with('success', 'Contact updated successfully!');
    }

    /**
     * Remove the specified contact
     */
    public function destroy(Request $request, Contact $contact)
    {
        $contactBook = $contact->contactBook;

        // Check if user can edit this contact book
        if (!$this->canUserEditContacts($contactBook)) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'You do not have permission to delete this contact.'
                ], 403);
            }
            abort(403);
        }

        $contact->delete();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Contact deleted successfully!'
            ]);
        }

        return redirect()->route('home', ['dashboard' => $contactBook->slug])
            ->with('success', 'Contact deleted successfully!');
    }

    /**
     * Get the current contact book based on the request
     */
    private function getCurrentContactBook(Request $request): ContactBook
    {
        $user = Auth::user();

        if ($dashboardSlug = $request->get('dashboard')) {
            $contactBook = ContactBook::findBySlug($dashboardSlug);

            if ($contactBook && $user->canAccessContactBook($contactBook)) {
                return $contactBook;
            }
        }

        // Default to personal contact book
        return $user->getOrCreatePersonalContactBook();
    }

    /**
     * Check if the current user can edit contacts in the given contact book
     */
    private function canUserEditContacts(ContactBook $contactBook): bool
    {
        $user = Auth::user();

        // Owner can always edit
        if ($contactBook->owner_id === $user->id) {
            return true;
        }

        // Check if user has admin access
        $userAccess = $contactBook->getUserAccess($user->email);
        return $userAccess && $userAccess->isAdmin();
    }

    /**
     * Process phones array from request
     */
    private function processPhones(?array $phones): ?array
    {
        if (!$phones) {
            return null;
        }

        $processedPhones = [];
        foreach ($phones as $phone) {
            if (!empty($phone['number']) && !empty($phone['type'])) {
                $processedPhones[] = [
                    'type' => $phone['type'],
                    'number' => $phone['number']
                ];
            }
        }

        return !empty($processedPhones) ? $processedPhones : null;
    }

    /**
     * Process emails array from request
     */
    private function processEmails(?array $emails): ?array
    {
        if (!$emails) {
            return null;
        }

        $processedEmails = [];
        foreach ($emails as $email) {
            if (!empty($email['address']) && !empty($email['type'])) {
                $processedEmails[] = [
                    'type' => $email['type'],
                    'address' => $email['address']
                ];
            }
        }

        return !empty($processedEmails) ? $processedEmails : null;
    }

    /**
     * Process websites array from request
     */
    private function processWebsites(?array $websites): ?array
    {
        if (!$websites) {
            return null;
        }

        $processedWebsites = [];
        foreach ($websites as $website) {
            if (!empty($website['url']) && !empty($website['type'])) {
                $url = $website['url'];

                // Ensure URL has protocol
                if (!preg_match('/^https?:\/\//', $url)) {
                    $url = 'https://' . $url;
                }

                $processedWebsites[] = [
                    'type' => $website['type'],
                    'url' => $url
                ];
            }
        }

        return !empty($processedWebsites) ? $processedWebsites : null;
    }
}
