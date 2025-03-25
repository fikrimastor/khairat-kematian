<?php

namespace Tests\Feature\Livewire;

use App\Enums\PaymentStatus;
use App\Livewire\Admin\PaymentVerification;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PaymentVerificationTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private User $member;

    private Payment $payment;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['is_admin' => true]);
        $this->member = User::factory()->create();
        $this->payment = Payment::factory()->create([
            'user_id' => $this->member->id,
            'status' => PaymentStatus::PENDING->value,
        ]);
    }

    #[Test]
    public function can_render_payment_verification_component()
    {
        $component = Livewire::actingAs($this->admin)
            ->test(PaymentVerification::class);

        $component->assertStatus(200);
        $component->assertSet('status', 'pending');
    }

    #[Test]
    public function can_search_payments()
    {
        $component = Livewire::actingAs($this->admin)
            ->test(PaymentVerification::class)
            ->set('search', $this->member->name);

        $component->assertSee($this->member->name);
    }

    #[Test]
    public function can_filter_payments_by_status()
    {
        $verifiedPayment = Payment::factory()->create([
            'user_id' => $this->member->id,
            'status' => PaymentStatus::VERIFIED->value,
        ]);

        $component = Livewire::actingAs($this->admin)
            ->test(PaymentVerification::class)
            ->set('status', PaymentStatus::VERIFIED->value);

        $component->assertSee($this->member->name);
    }

    #[Test]
    public function can_select_payment_for_verification()
    {
        $component = Livewire::actingAs($this->admin)
            ->test(PaymentVerification::class)
            ->call('selectPayment', $this->payment->id);

        $component->assertSet('selectedPayment.id', $this->payment->id);
    }

    #[Test]
    public function can_approve_payment()
    {
        $component = Livewire::actingAs($this->admin)
            ->test(PaymentVerification::class)
            ->call('selectPayment', $this->payment->id)
            ->call('approve');

        $this->payment->refresh();
        $this->assertEquals(PaymentStatus::VERIFIED->value, $this->payment->status->value ?? $this->payment->status);
    }

    #[Test]
    public function can_reject_payment()
    {
        $component = Livewire::actingAs($this->admin)
            ->test(PaymentVerification::class)
            ->call('selectPayment', $this->payment->id)
            ->set('verificationNotes', 'Payment rejected due to insufficient proof')
            ->call('reject');

        $this->payment->refresh();
        $this->assertEquals(PaymentStatus::REJECTED->value, $this->payment->status->value ?? $this->payment->status);
        $this->assertEquals('Payment rejected due to insufficient proof', $this->payment->notes);
    }

    #[Test]
    public function requires_notes_for_rejection()
    {
        $component = Livewire::actingAs($this->admin)
            ->test(PaymentVerification::class)
            ->call('selectPayment', $this->payment->id)
            ->call('reject');

        $component->assertHasErrors(['verificationNotes' => 'required']);
        $this->payment->refresh();
        $this->assertEquals(PaymentStatus::PENDING->value, $this->payment->status->value ?? $this->payment->status);
    }

    #[Test]
    public function non_admin_cannot_access_verification()
    {
        $this->withoutExceptionHandling();
        $this->expectException(\Illuminate\Auth\Access\AuthorizationException::class);

        Livewire::actingAs($this->member)
            ->test(PaymentVerification::class);
    }
}
