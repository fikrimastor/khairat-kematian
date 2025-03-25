<?php

namespace Tests\Unit;

use App\Actions\Payments\ProcessPaymentAction;
use App\DTOs\Payment\PaymentData;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Enums\PaymentType;
use App\Events\Payment\PaymentCreated;
use App\Models\Payment;
use App\Models\User;
use App\Services\Payment\Factories\PaymentGatewayFactory;
use App\Services\Payment\Providers\PaymentGatewayInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ProcessPaymentActionTest extends TestCase
{
    use RefreshDatabase;

    private ProcessPaymentAction $action;

    private User $user;

    private MockInterface $gatewayFactory;

    private MockInterface $gateway;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');

        $this->user = User::factory()->create();

        // Create a mock payment gateway that can be properly controlled
        $this->gateway = Mockery::mock(PaymentGatewayInterface::class);

        // Create a mock factory that returns our controlled gateway
        $this->gatewayFactory = Mockery::mock(PaymentGatewayFactory::class);
        $this->gatewayFactory->shouldReceive('make')
            ->withAnyArgs()
            ->andReturn($this->gateway);

        $this->action = new ProcessPaymentAction($this->gatewayFactory);
    }

    #[Test]
    public function it_processes_payment_successfully(): void
    {
        Event::fake();

        $paymentData = new PaymentData(
            userId: $this->user->id,
            amount: 50.00,
            paymentMethod: PaymentMethod::BANK_TRANSFER,
            paymentType: PaymentType::REGISTRATION,
            year: 2024,
            householdCount: 1,
            notes: 'Test payment',
            proofFile: UploadedFile::fake()->create('payment_proof.pdf', 100)
        );

        // Setup our controlled response
        $this->gateway->shouldReceive('processPayment')
            ->once()
            ->andReturn([
                'transaction_id' => 'TRX123',
                'status' => PaymentStatus::PENDING,
            ]);

        $payment = $this->action->execute($paymentData);

        $this->assertInstanceOf(Payment::class, $payment);
        $this->assertEquals($this->user->id, $payment->user_id);
        $this->assertEquals(50.00, $payment->amount);
        $this->assertEquals('TRX123', $payment->reference_no);
        $this->assertEquals(PaymentStatus::PENDING, $payment->status);

        $this->assertDatabaseHas('payment_proofs', [
            'payment_id' => $payment->id,
            'file_name' => 'payment_proof.pdf',
        ]);

        Event::assertDispatched(PaymentCreated::class, function ($event) use ($payment) {
            return $event->payment->id === $payment->id;
        });
    }

    #[Test]
    public function it_processes_payment_without_proof_file(): void
    {
        Event::fake();

        $paymentData = new PaymentData(
            userId: $this->user->id,
            amount: 40.00,
            paymentMethod: PaymentMethod::CHIP_IN_ASIA,
            paymentType: PaymentType::RENEWAL,
            year: 2024,
            householdCount: 2,
            notes: 'Test payment without proof',
            proofFile: null
        );

        // Setup our controlled response for ChipInAsia gateway
        $this->gateway->shouldReceive('processPayment')
            ->once()
            ->andReturn([
                'transaction_id' => 'TRX456',
                'status' => PaymentStatus::PENDING,
                'payment_url' => 'https://example.com/pay',
            ]);

        $payment = $this->action->execute($paymentData);

        $this->assertInstanceOf(Payment::class, $payment);
        $this->assertEquals($this->user->id, $payment->user_id);
        $this->assertEquals(40.00, $payment->amount);
        $this->assertEquals('TRX456', $payment->reference_no);
        $this->assertEquals(PaymentStatus::PENDING, $payment->status);

        $this->assertDatabaseMissing('payment_proofs', [
            'payment_id' => $payment->id,
        ]);

        Event::assertDispatched(PaymentCreated::class, function ($event) use ($payment) {
            return $event->payment->id === $payment->id;
        });
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
