<?php

namespace App\Http\Controllers;

use App\Models\Checkout;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InvoiceController extends Controller
{
    public function show(Request $request, Checkout $checkout): View
    {
        $user = $request->user();

        abort_unless($user->is_admin || $checkout->user_id === $user->id, 403);

        $checkout->load(['user', 'camp']);

        return view('invoice', ['checkout' => $checkout]);
    }
}