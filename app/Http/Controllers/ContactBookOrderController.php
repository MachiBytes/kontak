<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ContactBookOrderController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Update the order of contact books for the authenticated user
     */
    public function updateOrder(Request $request)
    {
        $request->validate([
            'contact_book_ids' => ['required', 'array'],
            'contact_book_ids.*' => ['integer', 'exists:contact_books,id'],
        ]);

        $user = Auth::user();
        $contactBookIds = $request->contact_book_ids;

        // Verify that the user has access to all the contact books they're trying to reorder
        $accessibleContactBooks = $user->allAccessibleContactBooks()->pluck('id')->toArray();

        $invalidIds = array_diff($contactBookIds, $accessibleContactBooks);
        if (!empty($invalidIds)) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have access to some of the contact books in this order.'
            ], 403);
        }

        // Filter out personal contact book from the ordering (it should always be first)
        $personalBook = $user->getOrCreatePersonalContactBook();
        $contactBookIds = array_filter($contactBookIds, function($id) use ($personalBook) {
            return $id !== $personalBook->id;
        });

        // Update the order
        $user->updateContactBookOrder($contactBookIds);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Contact book order updated successfully!'
            ]);
        }

        return redirect()->back()->with('success', 'Contact book order updated successfully!');
    }
}
