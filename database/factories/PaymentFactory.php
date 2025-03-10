<?php

namespace Database\Factories;

use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Payment>
 */
class PaymentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Payment::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $household = $this->faker->numberBetween(1, 8);
        $amountPerPerson = 20.00;  // RM20 per person as default

        return [
            'user_id' => User::factory(),
            'amount' => $household * $amountPerPerson,
            'payment_method' => $this->faker->randomElement(PaymentMethod::cases()),
            'reference_no' => $this->faker->regexify('[A-Z0-9]{12}'),
            'status' => $this->faker->randomElement(PaymentStatus::cases()),
            'payment_date' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'verified_by' => null,
            'verified_at' => null,
            'month' => $this->faker->monthName(),
            'year' => $this->faker->numberBetween(date('Y') - 1, date('Y')),
            'household_count' => $household,
            'notes' => $this->faker->optional(0.3)->sentence(),
        ];
    }

    /**
     * Indicate that the payment is pending.
     *
     * @return $this
     */
    public function pending(): self
    {
        return $this->state(fn (array $attributes) => [
            'status' => PaymentStatus::Pending,
            'verified_by' => null,
            'verified_at' => null,
        ]);
    }

    /**
     * Indicate that the payment is verified.
     *
     * @return $this
     */
    public function verified(): self
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => PaymentStatus::Verified,
                'verified_by' => User::factory()->create(['is_admin' => true])->id,
                'verified_at' => now(),
            ];
        });
    }

    /**
     * Indicate that the payment method is bank transfer.
     *
     * @return $this
     */
    public function bankTransfer(): self
    {
        return $this->state(fn (array $attributes) => [
            'payment_method' => PaymentMethod::BankTransfer,
        ]);
    }

    /**
     * Indicate that the payment method is online payment.
     *
     * @return $this
     */
    public function onlinePayment(): self
    {
        return $this->state(fn (array $attributes) => [
            'payment_method' => PaymentMethod::ChipInAsia,
        ]);
    }
}
