<?php

namespace App\Http\Controllers;

use App\Models\Camp;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $camps = Camp::with('benefits')->orderByDesc('price')->get();

        return view('welcome', ['camps' => $camps]);
    }
}