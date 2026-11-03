<?php

namespace App\Http\Controllers\Admin;

use App\Enums\PaymentStatus;
use App\Http\Controllers\Controller;
use App\Mail\Checkout\Paid;
use App\Models\Checkout;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    public function index(Request $request): View
    {
        $status = PaymentStatus::tryFrom((string) $request->query('status'));
        $search = trim((string) $request->query('q'));

        $checkouts = Checkout::with(['user', 'camp'])
            ->when($status, fn ($q) => $q->where('payment_status', $status->value))
            ->when($search !== '', function ($q) use ($search) {
                $q->whereHas('user', function ($u) use ($search) {
                    $u->where(function ($w) use ($search) {
                        $w->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
                });
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        // Satu query untuk semua hitungan status (hasilnya string mentah, bukan enum).
        $counts = DB::table('checkouts')
            ->whereNull('deleted_at')
            ->selectRaw('payment_status, count(*) as total')
            ->groupBy('payment_status')
            ->pluck('total', 'payment_status');

        $summary = [
            'waiting' => (int) ($counts[PaymentStatus::Waiting->value] ?? 0),
            'paid' => (int) ($counts[PaymentStatus::Paid->value] ?? 0),
            'canceled' => (int) ($counts[PaymentStatus::Canceled->value] ?? 0),
            // Pakai snapshot "amount", bukan harga camp saat ini.
            'revenue' => (int) Checkout::where('payment_status', PaymentStatus::Paid->value)->sum('amount'),
        ];

        return view('admin.dashboard', [
            'checkouts' => $checkouts,
            'summary' => $summary,
            'statuses' => PaymentStatus::cases(),
            'status' => $status,
            'search' => $search,
        ]);
    }

    public function update(Request $request, Checkout $checkout): RedirectResponse
    {
        $data = $request->validate([
            'payment_status' => [
                'required',
                Rule::enum(PaymentStatus::class)->only([PaymentStatus::Paid, PaymentStatus::Canceled]),
            ],
        ]);

        $new = PaymentStatus::from($data['payment_status']);

        // Kunci barisnya supaya dua admin tidak bisa mengubah status bersamaan.
        $updated = DB::transaction(function () use ($checkout, $new) {
            $locked = Checkout::whereKey($checkout->id)->lockForUpdate()->first();

            if (! $locked || $locked->payment_status !== PaymentStatus::Waiting) {
                return null;
            }

            $locked->update([
                'payment_status' => $new,
                'paid_at' => $new === PaymentStatus::Paid ? now() : null,
            ]);

            return $locked;
        });

        if (! $updated) {
            return back()->withErrors(['status' => 'Hanya tagihan berstatus waiting yang bisa diubah.']);
        }

        if ($new === PaymentStatus::Paid) {
            try {
                Mail::to($updated->user->email)->send(new Paid($updated));
            } catch (\Throwable $e) {
                report($e);
            }
        }

        return back()->with('success', 'Status ' . $updated->invoice_number . ' diperbarui.');
    }
}
