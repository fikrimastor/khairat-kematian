<?php

namespace Tests\Feature;

use App\Models\Payment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\Mocks\MockBankTransferGateway;
use Tests\Mocks\MockChipInAsiaGateway;
use Tests\TestCase;

class PaymentGatewayTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private Payment $payment;

    private MockChipInAsiaGateway $chipInAsiaGateway;

    private MockBankTransferGateway $bankTransferGateway;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->payment = Payment::factory()->create([
            'user_id' => $this->user->id,
            'status' => 'pending',
        ]);

        $this->chipInAsiaGateway = new MockChipInAsiaGateway;
        $this->bankTransferGateway = new MockBankTransferGateway;
    }

    #[Test]
    public function can_process_payment_through_chip_in_asia(): void
    {
        $result = $this->chipInAsiaGateway->processPayment([
            'amount' => 50.00,
            'reference' => $this->payment->id,
            'user_id' => $this->user->id,
            'payment_type' => 'registration',
        ]);

        $this->assertArrayHasKey('transaction_id', $result);
        $this->assertStringContainsString('MOCK-TRX-', $result['transaction_id']);
        $this->assertArrayHasKey('payment_url', $result);
    }

    #[Test]
    public function can_process_payment_through_bank_transfer(): void
    {
        $result = $this->bankTransferGateway->processPayment([
            'amount' => 40.00,
            'reference' => $this->payment->id,
            'user_id' => $this->user->id,
            'payment_type' => 'renewal',
        ]);

        $this->assertArrayHasKey('transaction_id', $result);
        $this->assertArrayHasKey('status', $result);
        $this->assertArrayHasKey('bank_details', $result);
        $this->assertEquals('BANK-'.$this->payment->id, $result['transaction_id']);
    }

    #[Test]
    public function can_verify_chip_in_asia_payment(): void
    {
        $result = $this->chipInAsiaGateway->verifyPayment('TRX123');

        $this->assertTrue($result['success']);
        $this->assertEquals('completed', $result['status']);
        $this->assertEquals(50.00, $result['amount']);
    }

    #[Test]
    public function validates_chip_in_asia_signature(): void
    {
        $payload = [
            'transaction_id' => 'TRX123',
            'status' => 'completed',
            'amount' => 50.00,
        ];

        $validSignature = 'valid-signature';

        $result = $this->chipInAsiaGateway->validateSignature($payload, $validSignature);

        $this->assertTrue($result);
    }

    #[Test]
    public function rejects_invalid_chip_in_asia_signature(): void
    {
        $payload = [
            'transaction_id' => 'TRX123',
            'status' => 'completed',
            'amount' => 50.00,
        ];

        $invalidSignature = 'invalid-signature';

        $result = $this->chipInAsiaGateway->validateSignature($payload, $invalidSignature);

        $this->assertFalse($result);
    }
}
