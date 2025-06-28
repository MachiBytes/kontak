<?php

namespace App\Providers;

use App\Models\ContactBook;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ViewComposerServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Share contact book data with navigation
        View::composer('layouts.navigation', function ($view) {
            if (Auth::check()) {
                $user = Auth::user();

                // Get all accessible contact books with user's custom ordering
                $contactBooks = $user->allAccessibleContactBooksOrdered();

                // Get the current dashboard slug
                $currentDashboard = request()->get('dashboard');

                // Determine current contact book
                $currentContactBook = null;
                if ($currentDashboard) {
                    $currentContactBook = $this->getContactBookBySlug($currentDashboard);
                }

                if (!$currentContactBook) {
                    $currentContactBook = $user->getOrCreatePersonalContactBook();
                }

                $view->with([
                    'contactBooks' => $contactBooks,
                    'currentContactBook' => $currentContactBook,
                    'currentDashboard' => $currentDashboard
                ]);
            }
        });
    }

    /**
     * Get contact book by dashboard slug
     */
    private function getContactBookBySlug(string $slug): ?ContactBook
    {
        return ContactBook::findBySlug($slug);
    }
}
