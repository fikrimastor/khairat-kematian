<?php

namespace Database\Factories;

use App\Models\Payment;
use App\Models\Receipt;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Receipt>
 */
class ReceiptFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Receipt::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'payment_id' => Payment::factory(),
            'receipt_number' => 'RESIT-'.$this->faker->date('Ymd').'-'.$this->faker->randomNumber(4),
            'receipt_path' => $this->faker->optional(0.7)->filePath(),
            'generated_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
        ];
    }

    /**
     * Indicate that the receipt has a file path.
     *
     * @return $this
     */
    public function withFile(): self
    {
        return $this->state(fn (array $attributes) => [
            'receipt_path' => 'receipts/'.$this->faker->uuid.'.pdf',
        ]);
    }

    /**
     * Indicate that the receipt is for a specific payment.
     *
     * @return $this
     */
    public function forPayment(Payment $payment): self
    {
        return $this->state(fn (array $attributes) => [
            'payment_id' => $payment->id,
        ]);
    }
}
