<?php

namespace Database\Factories;

use App\Models\Payment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PaymentProof>
 */
class PaymentProofFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $fileTypes = ['image/jpeg', 'image/png', 'application/pdf'];
        $fileType = $this->faker->randomElement($fileTypes);
        $extension = match ($fileType) {
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'application/pdf' => 'pdf',
        };

        return [
            'payment_id' => Payment::factory(),
            'file_path' => 'payment_proofs/'.$this->faker->uuid.'.'.$extension,
            'file_name' => 'payment_proof_'.$this->faker->word.'.'.$extension,
            'file_type' => $fileType,
            'file_size' => $this->faker->numberBetween(50000, 5000000),
        ];
    }
}
