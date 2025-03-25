<?php

namespace Tests\Feature;

use App\Actions\Admin\VerifyPaymentAction;
use App\Enums\PaymentStatus;
use App\Models\Payment;
use App\Models\User;
use App\Notifications\PaymentConfirmedNotification;
use App\Notifications\PaymentRejectedNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
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
        Event::fake();
        Notification::fake();

        $this->admin = User::factory()->create(['is_admin' => true]);
        $this->member = User::factory()->create();
        $this->payment = Payment::factory()->create([
            'user_id' => $this->member->id,
            'status' => PaymentStatus::PENDING->value,
        ]);
    }

    #[Test]
    public function admin_can_verify_payment_successfully(): void
    {
        $this->actingAs($this->admin);
        $action = app(VerifyPaymentAction::class);

        $action->execute(
            $this->payment->id,
            $this->admin->id,
            true,
            'Payment verified successfully'
        );

        $this->payment->refresh();
        $this->assertEquals(PaymentStatus::VERIFIED, $this->payment->status);
        $this->assertEquals($this->admin->id, $this->payment->verified_by);
        $this->assertEquals('Payment verified successfully', $this->payment->notes);

        $this->assertDatabaseHas('receipts', [
            'payment_id' => $this->payment->id,
        ]);

        Notification::assertSentTo(
            $this->member,
            PaymentConfirmedNotification::class,
            function ($notification) {
                return $notification->payment->id === $this->payment->id;
            }
        );
    }

    #[Test]
    public function admin_can_reject_payment(): void
    {
        $this->actingAs($this->admin);
        $action = app(VerifyPaymentAction::class);

        $action->execute(
            $this->payment->id,
            $this->admin->id,
            false,
            'Payment rejected due to invalid proof'
        );

        $this->payment->refresh();
        $this->assertEquals(PaymentStatus::REJECTED, $this->payment->status);
        $this->assertEquals($this->admin->id, $this->payment->verified_by);
        $this->assertEquals('Payment rejected due to invalid proof', $this->payment->notes);

        $this->assertDatabaseMissing('receipts', [
            'payment_id' => $this->payment->id,
        ]);

        Notification::assertSentTo(
            $this->member,
            PaymentRejectedNotification::class,
            function ($notification) {
                return $notification->payment->id === $this->payment->id;
            }
        );
    }

    #[Test]
    public function verification_requires_admin_user(): void
    {
        $action = app(VerifyPaymentAction::class);

        $regularUser = User::factory()->create(['is_admin' => false]);

        $this->expectException(\Illuminate\Auth\Access\AuthorizationException::class);

        $action->execute(
            $this->payment->id,
            $regularUser->id,
            true,
            'Payment verified'
        );
    }

    #[Test]
    public function verification_requires_existing_payment(): void
    {
        $action = app(VerifyPaymentAction::class);

        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);

        $action->execute(
            9999, // Non-existent payment ID
            $this->admin->id,
            true,
            'Payment verified'
        );
    }
}
