<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $checkouts = $request->user()
            ->checkouts()
            ->with('camp')
            ->latest()
            ->get();

        return view('dashboard', ['checkouts' => $checkouts]);
    }
}