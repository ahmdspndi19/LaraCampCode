<?php

namespace App\Http\Controllers;

use App\Enums\PaymentStatus;
use App\Http\Requests\StoreCheckoutRequest;
use App\Mail\Checkout\AfterCheckout;
use App\Models\Camp;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    public function create(Camp $camp): View
    {
        $camp->load('benefits');

        return view('checkout', ['camp' => $camp]);
    }

    public function store(StoreCheckoutRequest $request, Camp $camp): RedirectResponse
    {
        $data = $request->validated();
        $user = $request->user();

        $checkout = DB::transaction(function () use ($user, $camp, $data, $request) {
            // Satu user hanya boleh punya satu tagihan aktif (waiting/paid) per camp.
            $existing = $user->checkouts()
                ->where('camp_id', $camp->id)
                ->whereIn('payment_status', [
                    PaymentStatus::Waiting->value,
                    PaymentStatus::Paid->value,
                ])
                ->latest()
                ->lockForUpdate()
                ->first();

            if ($existing) {
                return $existing;
            }

            $user->update([
                'name' => $data['name'],
                'occupation' => $data['occupation'],
            ]);

            return $user->checkouts()->create([
                'camp_id' => $camp->id,
                'card_last_four' => substr($data['card_number'], -4),
                'amount' => $camp->price,
                'expired' => $request->expiryDate(),
                'payment_status' => PaymentStatus::Waiting,
            ]);
        });

        if (! $checkout->wasRecentlyCreated) {
            $message = $checkout->payment_status === PaymentStatus::Paid
                ? 'Kamu sudah terdaftar di bootcamp ini.'
                : 'Kamu masih punya tagihan menunggu pembayaran untuk bootcamp ini.';

            return back()
                ->withErrors(['camp' => $message])
                ->withInput($request->safe()->except(['card_number', 'cvc']));
        }

        try {
            Mail::to($user->email)->send(new AfterCheckout($checkout));
        } catch (\Throwable $e) {
            report($e);
        }

        return redirect()
            ->route('success_checkout')
            ->with('invoice', $checkout->id);
    }

    public function success(): View
    {
        return view('success_checkout');
    }
}
