<?php

namespace App\Http\Controllers;

use App\Mail\Checkout\AfterCheckout;
use App\Models\Camp;
use App\Models\Checkout;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    public function create(Camp $camp): View
    {
        return view('checkout', ['camp' => $camp]);
    }

    public function store(Request $request, Camp $camp): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'occupation' => ['required', 'string', 'max:255'],
            'card_number' => ['required', 'digits_between:13,19'],
            'expired' => ['required', 'regex:/^(0[1-9]|1[0-2])\/(\d{2}|\d{4})$/'],
            'cvc' => ['required', 'digits_between:3,4'],
        ]);

        $expired = Carbon::createFromFormat(
            strlen($data['expired']) === 5 ? '!m/y' : '!m/Y',
            $data['expired']
        )->endOfMonth();

        if ($expired->isPast()) {
            return back()
                ->withErrors(['expired' => 'Kartu sudah kedaluwarsa.'])
                ->withInput();
        }

        $user = $request->user();

        $hasPending = $user->checkouts()
            ->where('camp_id', $camp->id)
            ->where('payment_status', Checkout::WAITING)
            ->exists();

        if ($hasPending) {
            return back()
                ->withErrors(['camp' => 'Kamu masih punya tagihan menunggu pembayaran untuk bootcamp ini.'])
                ->withInput();
        }

        $checkout = DB::transaction(function () use ($user, $camp, $data, $expired) {
            $user->update([
                'name' => $data['name'],
                'occupation' => $data['occupation'],
            ]);

            return $user->checkouts()->create([
                'camp_id' => $camp->id,
                'card_number' => $data['card_number'],
                'expired' => $expired,
                'cvc' => $data['cvc'],
                'payment_status' => Checkout::WAITING,
            ]);
        });

        try {
            Mail::to($user->email)->send(new AfterCheckout($checkout));
        } catch (\Throwable $e) {
            report($e);
        }

        return redirect()->route('success_checkout');
    }

    public function success(): View
    {
        return view('success_checkout');
    }
}