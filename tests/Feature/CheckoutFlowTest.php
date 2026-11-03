<?php

namespace Tests\Feature;

use App\Mail\Checkout\AfterCheckout;
use App\Models\Camp;
use App\Models\Checkout;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class CheckoutFlowTest extends TestCase
{
    use RefreshDatabase;

    private function payload(array $override = []): array
    {
        return array_merge([
            'name' => 'Budi Santoso',
            'occupation' => 'Mahasiswa',
            'card_number' => '4242 4242 4242 4242',
            'expired' => '12/' . now()->addYears(2)->format('y'),
            'cvc' => '123',
        ], $override);
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $camp = Camp::factory()->create();

        $this->get(route('checkout', $camp))->assertRedirect(route('login'));
    }

    public function test_user_can_checkout_and_full_card_is_never_stored(): void
    {
        Mail::fake();
        $user = User::factory()->create();
        $camp = Camp::factory()->create(['price' => 280000]);

        $this->actingAs($user)
            ->post(route('checkout.store', $camp), $this->payload())
            ->assertRedirect(route('success_checkout'));

        $this->assertDatabaseHas('checkouts', [
            'user_id' => $user->id,
            'camp_id' => $camp->id,
            'card_last_four' => '4242',
            'amount' => 280000,
            'payment_status' => 'waiting',
        ]);

        $this->assertFalse(Schema::hasColumn('checkouts', 'card_number'));
        $this->assertFalse(Schema::hasColumn('checkouts', 'cvc'));

        Mail::assertSent(AfterCheckout::class);
    }

    public function test_amount_is_a_snapshot_of_the_price_at_checkout_time(): void
    {
        Mail::fake();
        $user = User::factory()->create();
        $camp = Camp::factory()->create(['price' => 140000]);

        $this->actingAs($user)->post(route('checkout.store', $camp), $this->payload());
        $camp->update(['price' => 999000]);

        $this->assertSame(140000, Checkout::first()->amount);
    }

    public function test_expired_card_is_rejected(): void
    {
        $user = User::factory()->create();
        $camp = Camp::factory()->create();

        $this->actingAs($user)
            ->post(route('checkout.store', $camp), $this->payload(['expired' => '01/20']))
            ->assertSessionHasErrors('expired');

        $this->assertDatabaseCount('checkouts', 0);
    }

    public function test_cannot_create_second_checkout_while_one_is_waiting(): void
    {
        Mail::fake();
        $user = User::factory()->create();
        $camp = Camp::factory()->create();
        Checkout::factory()->create(['user_id' => $user->id, 'camp_id' => $camp->id]);

        $this->actingAs($user)
            ->post(route('checkout.store', $camp), $this->payload())
            ->assertSessionHasErrors('camp');

        $this->assertDatabaseCount('checkouts', 1);
    }

    public function test_cannot_buy_a_camp_that_is_already_paid(): void
    {
        Mail::fake();
        $user = User::factory()->create();
        $camp = Camp::factory()->create();
        Checkout::factory()->paid()->create(['user_id' => $user->id, 'camp_id' => $camp->id]);

        $this->actingAs($user)
            ->post(route('checkout.store', $camp), $this->payload())
            ->assertSessionHasErrors('camp');

        $this->assertDatabaseCount('checkouts', 1);
    }

    public function test_invoice_is_only_visible_to_owner_or_admin(): void
    {
        $owner = User::factory()->create();
        $stranger = User::factory()->create();
        $admin = User::factory()->create(['is_admin' => true]);
        $checkout = Checkout::factory()->create(['user_id' => $owner->id]);

        $this->actingAs($owner)->get(route('invoice', $checkout))->assertOk();
        $this->actingAs($admin)->get(route('invoice', $checkout))->assertOk();
        $this->actingAs($stranger)->get(route('invoice', $checkout))->assertForbidden();
    }
}
