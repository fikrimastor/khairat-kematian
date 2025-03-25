<?php

namespace Tests\Feature;

use App\Models\Payment;
use App\Models\Receipt;
use App\Models\User;
use App\Notifications\PaymentConfirmedNotification;
use App\Notifications\PaymentRejectedNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Notification;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class NotificationTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private Payment $payment;

    private Receipt $receipt;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->payment = Payment::factory()->create([
            'user_id' => $this->user->id,
            'status' => 'pending',
        ]);
        $this->receipt = Receipt::factory()->create([
            'payment_id' => $this->payment->id,
            'receipt_number' => 'R-'.date('Y').'-'.str_pad($this->payment->id, 6, '0', STR_PAD_LEFT),
            'generated_at' => now(),
        ]);
    }

    #[Test]
    public function user_receives_payment_confirmed_notification(): void
    {
        Notification::fake();

        $this->payment->update(['status' => 'verified']);
        $this->user->notify(new PaymentConfirmedNotification($this->payment, $this->receipt));

        Notification::assertSentTo(
            $this->user,
            PaymentConfirmedNotification::class,
            function ($notification) {
                return $notification->payment->id === $this->payment->id
                    && $notification->receipt->id === $this->receipt->id;
            }
        );
    }

    #[Test]
    public function user_receives_payment_rejected_notification(): void
    {
        Notification::fake();

        $this->payment->update(['status' => 'rejected']);
        $this->user->notify(new PaymentRejectedNotification($this->payment));

        Notification::assertSentTo(
            $this->user,
            PaymentRejectedNotification::class,
            function ($notification) {
                return $notification->payment->id === $this->payment->id;
            }
        );
    }

    #[Test]
    public function user_can_mark_notification_as_read(): void
    {
        // Create an actual database notification
        $notification = $this->createDatabaseNotification($this->user);

        $response = $this->actingAs($this->user)
            ->post(route('notifications.mark-read', ['id' => $notification->id]));

        $response->assertRedirect();
        $response->assertSessionHas('status', 'Notification marked as read.');

        $this->assertNotNull($notification->fresh()->read_at);
    }

    #[Test]
    public function user_can_mark_all_notifications_as_read(): void
    {
        // Create multiple database notifications
        $this->createDatabaseNotification($this->user);
        $this->createDatabaseNotification($this->user);

        $response = $this->actingAs($this->user)
            ->post(route('notifications.mark-all-read'));

        $response->assertRedirect();
        $response->assertSessionHas('status', 'All notifications marked as read.');

        // Refresh the user to get the latest notification status
        $this->user->refresh();
        $this->assertEquals(0, $this->user->unreadNotifications->count());
    }

    #[Test]
    public function user_can_delete_notification(): void
    {
        // Create an actual database notification
        $notification = $this->createDatabaseNotification($this->user);

        $response = $this->actingAs($this->user)
            ->delete(route('notifications.delete', ['id' => $notification->id]));

        $response->assertRedirect();
        $response->assertSessionHas('status', 'Notification deleted.');

        $this->assertDatabaseMissing('notifications', ['id' => $notification->id]);
    }

    #[Test]
    public function user_can_delete_all_notifications(): void
    {
        // Create multiple database notifications
        $this->createDatabaseNotification($this->user);
        $this->createDatabaseNotification($this->user);

        $response = $this->actingAs($this->user)
            ->delete(route('notifications.delete-all'));

        $response->assertRedirect();
        $response->assertSessionHas('status', 'All notifications deleted.');

        $this->assertEquals(0, $this->user->notifications->count());
    }

    #[Test]
    public function user_can_view_notification_history(): void
    {
        // Create multiple database notifications
        $this->createDatabaseNotification($this->user);
        $this->createDatabaseNotification($this->user);

        $response = $this->actingAs($this->user)
            ->get(route('notifications.history'));

        $response->assertStatus(200);
        $response->assertViewIs('notifications.history');
        $response->assertViewHas('notifications');
        $this->assertEquals(2, $response->viewData('notifications')->count());
    }

    /**
     * Helper method to create a database notification
     */
    private function createDatabaseNotification(User $user): DatabaseNotification
    {
        return DatabaseNotification::create([
            'id' => \Illuminate\Support\Str::uuid()->toString(),
            'type' => PaymentConfirmedNotification::class,
            'notifiable_type' => get_class($user),
            'notifiable_id' => $user->id,
            'data' => [
                'payment_id' => $this->payment->id,
                'receipt_id' => $this->receipt->id,
                'amount' => $this->payment->amount,
                'message' => 'Your payment has been verified and confirmed.',
                'type' => 'payment_confirmed',
            ],
            'read_at' => null,
        ]);
    }
}
