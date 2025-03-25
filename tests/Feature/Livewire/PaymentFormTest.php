<?php

namespace Tests\Feature\Livewire;

use App\Livewire\Payment\PaymentForm;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PaymentFormTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
        $this->user = User::factory()->create();
    }

    #[Test]
    public function can_render_payment_form()
    {
        $component = Livewire::actingAs($this->user)
            ->test(PaymentForm::class);

        $component->assertStatus(200);
        $component->assertSet('paymentType', $this->user->getPaymentType());
    }

    #[Test]
    public function can_submit_payment_form()
    {
        $component = Livewire::actingAs($this->user)
            ->test(PaymentForm::class)
            ->set('paymentType', 'registration')
            ->set('paymentMethod', 'bank_transfer')
            ->set('amount', 50)
            ->set('reference', 'Test payment submission')
            ->call('save');

        $component->assertHasNoErrors();
    }

    #[Test]
    public function validates_required_fields()
    {
        $component = Livewire::actingAs($this->user)
            ->test(PaymentForm::class)
            ->set('paymentType', '')
            ->set('paymentMethod', '')
            ->set('amount', '')
            ->call('save');

        $component->assertHasErrors([
            'paymentType' => 'required',
            'paymentMethod' => 'required',
            'amount' => 'required',
        ]);
    }

    #[Test]
    public function validates_payment_method()
    {
        $component = Livewire::actingAs($this->user)
            ->test(PaymentForm::class)
            ->set('paymentType', 'registration')
            ->set('paymentMethod', 'invalid_method')
            ->set('amount', 50)
            ->call('save');

        $component->assertHasErrors([
            'paymentMethod' => 'in',
        ]);
    }

    #[Test]
    public function validates_amount_min()
    {
        $component = Livewire::actingAs($this->user)
            ->test(PaymentForm::class)
            ->set('paymentType', 'registration')
            ->set('paymentMethod', 'bank_transfer')
            ->set('amount', 0)
            ->call('save');

        $component->assertHasErrors([
            'amount' => 'min',
        ]);
    }

    #[Test]
    public function validates_reference_length()
    {
        $component = Livewire::actingAs($this->user)
            ->test(PaymentForm::class)
            ->set('paymentType', 'registration')
            ->set('paymentMethod', 'bank_transfer')
            ->set('amount', 50)
            ->set('reference', str_repeat('a', 256))
            ->call('save');

        $component->assertHasErrors([
            'reference' => 'max',
        ]);
    }
}
