<?php

namespace Tests\Feature;

use App\Actions\Payments\GenerateReceiptAction;
use App\Models\Payment;
use App\Models\Receipt;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ReceiptGenerationTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private Payment $payment;

    private GenerateReceiptAction $action;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');

        $this->admin = User::factory()->create(['is_admin' => true]);
        $this->payment = Payment::factory()->create([
            'status' => 'verified',
            'amount' => 50.00,
            'payment_date' => now(),
        ]);
        $this->action = app(GenerateReceiptAction::class);
    }

    #[Test]
    public function can_generate_receipt(): void
    {
        $this->actingAs($this->admin);
        $receipt = $this->action->execute($this->payment->id);

        $this->assertInstanceOf(Receipt::class, $receipt);
        $this->assertEquals($this->payment->id, $receipt->payment_id);
        $this->assertNotNull($receipt->receipt_number);
        $this->assertNotNull($receipt->receipt_path);
        $this->assertTrue(Storage::disk('public')->exists($receipt->receipt_path));
    }

    #[Test]
    public function receipt_number_follows_format(): void
    {
        $this->actingAs($this->admin);
        $receipt = $this->action->execute($this->payment->id);

        $this->assertMatchesRegularExpression(
            '/^R-'.date('Y').'-\d{6}$/',
            $receipt->receipt_number
        );
    }

    #[Test]
    public function cannot_generate_receipt_for_unverified_payment(): void
    {
        $this->actingAs($this->admin);
        $unverifiedPayment = Payment::factory()->create([
            'status' => 'pending',
            'amount' => 50.00,
            'payment_date' => now(),
        ]);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Cannot generate receipt for unverified payment');

        $this->action->execute($unverifiedPayment->id);
    }

    #[Test]
    public function non_admin_cannot_generate_receipt(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $this->expectException(\Illuminate\Auth\Access\AuthorizationException::class);

        $this->action->execute($this->payment->id);
    }

    #[Test]
    public function receipt_contains_correct_payment_details(): void
    {
        $this->actingAs($this->admin);
        $receipt = $this->action->execute($this->payment->id);

        $this->assertEquals($this->payment->amount, $receipt->payment->amount);
        $this->assertEquals($this->payment->payment_date, $receipt->payment->payment_date);
        $this->assertEquals($this->payment->payment_method, $receipt->payment->payment_method);
        $this->assertEquals($this->payment->payment_type, $receipt->payment->payment_type);
    }

    #[Test]
    public function receipt_file_is_pdf(): void
    {
        $this->actingAs($this->admin);
        $receipt = $this->action->execute($this->payment->id);

        $this->assertEquals('application/pdf', Storage::disk('public')->mimeType($receipt->receipt_path));
    }

    #[Test]
    public function receipt_file_has_correct_name(): void
    {
        $this->actingAs($this->admin);
        $receipt = $this->action->execute($this->payment->id);

        $this->assertEquals(
            'receipts/'.$receipt->receipt_number.'.pdf',
            $receipt->receipt_path
        );
    }
}
