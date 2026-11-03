<?php

namespace Tests\Feature;

use App\Mail\Checkout\Paid;
use App\Models\Checkout;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class AdminCheckoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_regular_user_cannot_open_admin_dashboard(): void
    {
        $this->actingAs(User::factory()->create())
            ->get(route('admin.dashboard'))
            ->assertForbidden();
    }

    public function test_admin_sees_checkouts_and_can_filter_by_status(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $waiting = Checkout::factory()->create();
        $paid = Checkout::factory()->paid()->create();

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee($waiting->invoice_number)
            ->assertSee($paid->invoice_number);

        $this->actingAs($admin)
            ->get(route('admin.dashboard', ['status' => 'paid']))
            ->assertOk()
            ->assertSee($paid->invoice_number)
            ->assertDontSee($waiting->invoice_number);
    }

    public function test_admin_can_mark_paid_once_and_user_gets_an_email(): void
    {
        Mail::fake();
        $admin = User::factory()->create(['is_admin' => true]);
        $checkout = Checkout::factory()->create();

        $this->actingAs($admin)
            ->patch(route('admin.checkout.update', $checkout), ['payment_status' => 'paid'])
            ->assertSessionHas('success');

        $checkout->refresh();
        $this->assertSame('paid', $checkout->payment_status->value);
        $this->assertNotNull($checkout->paid_at);
        Mail::assertSent(Paid::class);

        // Status final: tidak bisa diubah lagi.
        $this->actingAs($admin)
            ->patch(route('admin.checkout.update', $checkout), ['payment_status' => 'canceled'])
            ->assertSessionHasErrors('status');

        $this->assertSame('paid', $checkout->fresh()->payment_status->value);
    }

    public function test_admin_cannot_set_an_arbitrary_status(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $checkout = Checkout::factory()->create();

        $this->actingAs($admin)
            ->patch(route('admin.checkout.update', $checkout), ['payment_status' => 'waiting'])
            ->assertSessionHasErrors('payment_status');
    }

    public function test_registration_cannot_make_someone_an_admin(): void
    {
        $this->post(route('register.store'), [
            'name' => 'Mallory',
            'email' => 'mallory@example.test',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'is_admin' => 1,
        ])->assertRedirect(route('dashboard'));

        $this->assertFalse(User::where('email', 'mallory@example.test')->first()->is_admin);
    }
}
