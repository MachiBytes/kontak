<?php

namespace App\Http\Controllers;

use App\Models\ContactBook;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ContactBookController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Update contact book information
     */
    public function update(Request $request, ContactBook $contactBook)
    {
        // Check if user can edit this contact book
        if (!Auth::user()->canEditContactBook($contactBook)) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'You do not have permission to edit this contact book.'
                ], 403);
            }
            abort(403);
        }

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'color' => ['required', 'string', 'in:blue,green,purple,pink,orange,red,yellow,indigo'],
        ]);

        $contactBook->update([
            'name' => $request->name,
            'color' => $request->color,
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Contact book updated successfully!',
                'contactBook' => $contactBook->fresh()
            ]);
        }

        return redirect()->back()
            ->with('success', 'Contact book updated successfully!');
    }

    /**
     * Get contact book by slug
     */
    public function getBySlug(string $slug): ?ContactBook
    {
        return ContactBook::findBySlug($slug);
    }

    /**
     * Get the current contact book based on the request
     */
    public function getCurrentContactBook(Request $request): ContactBook
    {
        $user = Auth::user();

        if ($dashboardSlug = $request->get('dashboard')) {
            $contactBook = $this->getBySlug($dashboardSlug);

            if ($contactBook && $user->canAccessContactBook($contactBook)) {
                return $contactBook;
            }
        }

        // Default to personal contact book
        return $user->getOrCreatePersonalContactBook();
    }
}
