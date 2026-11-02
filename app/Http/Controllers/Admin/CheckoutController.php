<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Checkout;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    public function index(): View
    {
        $checkouts = Checkout::with(['user', 'camp'])->latest()->paginate(15);

        $summary = [
            'waiting' => Checkout::where('payment_status', Checkout::WAITING)->count(),
            'paid' => Checkout::where('payment_status', Checkout::PAID)->count(),
            'canceled' => Checkout::where('payment_status', Checkout::CANCELED)->count(),
            'revenue' => DB::table('checkouts')
                ->join('camps', 'camps.id', '=', 'checkouts.camp_id')
                ->where('checkouts.payment_status', Checkout::PAID)
                ->whereNull('checkouts.deleted_at')
                ->sum('camps.price'),
        ];

        return view('admin.dashboard', compact('checkouts', 'summary'));
    }

    public function update(Request $request, Checkout $checkout): RedirectResponse
    {
        $data = $request->validate([
            'payment_status' => ['required', Rule::in([Checkout::PAID, Checkout::CANCELED])],
        ]);

        if ($checkout->payment_status !== Checkout::WAITING) {
            return back()->withErrors(['status' => 'Hanya tagihan berstatus waiting yang bisa diubah.']);
        }

        $checkout->update($data);

        return back()->with('success', 'Status ' . $checkout->invoice_number . ' diperbarui.');
    }
}