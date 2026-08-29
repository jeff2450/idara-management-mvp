<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Dashibodi rahisi: mtumiaji anapoingia anaona idara zake tu - angalia
 * architecture-essentials.md, hatua ya 5 ya MVP.
 */
class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        $departments = $user->isAdmin()
            ? \App\Models\Department::withCount('users')->orderBy('name')->get()
            : $user->departments()->withCount('users')->orderBy('name')->get();

        return view('dashboard', [
            'departments' => $departments,
        ]);
    }
}
