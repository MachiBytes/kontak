<?php

namespace App\Http\Controllers;

use App\Models\ContactBook;
use App\Models\UserAccess;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class SettingsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display the settings page
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // Get the current contact book
        $contactBook = $this->getCurrentContactBook($request);

        // Get user access for this contact book
        $userAccess = UserAccess::where('contact_book_id', $contactBook->id)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('settings.index', compact('user', 'userAccess', 'contactBook'));
    }

    /**
     * Update user profile
     */
    public function updateProfile(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'phone_number' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:500'],
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'address' => $request->address,
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Profile updated successfully!',
                'user' => $user->fresh()
            ]);
        }

        return redirect()->route('settings.index')
            ->with('success', 'Profile updated successfully!');
    }

    /**
     * Update user password
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'errors' => ['current_password' => ['The current password is incorrect.']]
                ], 422);
            }
            return back()->withErrors(['current_password' => 'The current password is incorrect.']);
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Password updated successfully!'
            ]);
        }

        return redirect()->route('settings.index')
            ->with('success', 'Password updated successfully!');
    }

    /**
     * Update contact book information
     */
    public function updateContactBook(Request $request)
    {
        $contactBook = $this->getCurrentContactBook($request);

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

        return redirect()->route('settings.index')
            ->with('success', 'Contact book updated successfully!');
    }

    /**
     * Add user access
     */
    public function addUserAccess(Request $request)
    {
        $contactBook = $this->getCurrentContactBook($request);

        // Check if user can edit this contact book
        if (!Auth::user()->canEditContactBook($contactBook)) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'You do not have permission to modify access for this contact book.'
                ], 403);
            }
            abort(403);
        }

        $request->validate([
            'email' => ['required', 'email', 'max:255'],
            'role' => ['required', 'in:admin,audience'],
        ]);

        // Check if access already exists
        $existingAccess = UserAccess::where('contact_book_id', $contactBook->id)
            ->where('email', $request->email)
            ->first();

        if ($existingAccess) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'errors' => ['email' => ['This user already has access to this contact book.']]
                ], 422);
            }
            return back()->withErrors(['email' => 'This user already has access to this contact book.']);
        }

        // Prevent user from adding themselves
        if ($request->email === Auth::user()->email) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'errors' => ['email' => ['You cannot add yourself to the access list.']]
                ], 422);
            }
            return back()->withErrors(['email' => 'You cannot add yourself to the access list.']);
        }

        $userAccess = UserAccess::create([
            'contact_book_id' => $contactBook->id,
            'owner_id' => $contactBook->owner_id, // Add for backward compatibility
            'email' => $request->email,
            'role' => $request->role,
            'invited_at' => now(),
        ]);

        if ($request->ajax()) {
            $userAccess->load('user');
            return response()->json([
                'success' => true,
                'message' => 'User access added successfully!',
                'userAccess' => $userAccess
            ]);
        }

        return redirect()->route('settings.index')
            ->with('success', 'User access added successfully!');
    }

    /**
     * Update user access role
     */
    public function updateUserAccess(Request $request, UserAccess $userAccess)
    {
        // Ensure the user can edit this contact book
        if (!Auth::user()->canEditContactBook($userAccess->contactBook)) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized action.'
                ], 403);
            }
            abort(403);
        }

        $request->validate([
            'role' => ['required', 'in:admin,audience'],
        ]);

        $userAccess->update([
            'role' => $request->role,
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'User access updated successfully!',
                'userAccess' => $userAccess->fresh()
            ]);
        }

        return redirect()->route('settings.index')
            ->with('success', 'User access updated successfully!');
    }

    /**
     * Remove user access
     */
    public function removeUserAccess(UserAccess $userAccess)
    {
        // Ensure the user can edit this contact book
        if (!Auth::user()->canEditContactBook($userAccess->contactBook)) {
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized action.'
                ], 403);
            }
            abort(403);
        }

        $userAccess->delete();

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'User access removed successfully!'
            ]);
        }

        return redirect()->route('settings.index')
            ->with('success', 'User access removed successfully!');
    }

    /**
     * Bulk remove user access
     */
    public function bulkRemoveUserAccess(Request $request)
    {
        $contactBook = $this->getCurrentContactBook($request);

        // Check if user can edit this contact book
        if (!Auth::user()->canEditContactBook($contactBook)) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'You do not have permission to modify access for this contact book.'
                ], 403);
            }
            abort(403);
        }

        $request->validate([
            'user_access_ids' => ['required', 'array'],
            'user_access_ids.*' => ['integer', 'exists:user_access,id'],
        ]);

        $removedCount = UserAccess::where('contact_book_id', $contactBook->id)
            ->whereIn('id', $request->user_access_ids)
            ->delete();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "{$removedCount} user(s) removed successfully!",
                'removed_count' => $removedCount
            ]);
        }

        return redirect()->route('settings.index')
            ->with('success', "{$removedCount} user(s) removed successfully!");
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
     * Get contact book by slug
     */
    private function getContactBookBySlug(string $slug): ?ContactBook
    {
        return ContactBook::findBySlug($slug);
    }
}
