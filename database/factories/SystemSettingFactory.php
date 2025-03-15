<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SystemSetting>
 */
class SystemSettingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'key' => $this->faker->unique()->word,
            'value' => $this->faker->sentence,
            'description' => $this->faker->paragraph,
        ];
    }

    /**
     * Configure the factory to create a fee setting.
     */
    public function fee(): self
    {
        return $this->state(function () {
            return [
                'key' => $this->faker->randomElement(['registration_fee', 'renewal_fee']),
                'value' => (string) $this->faker->numberBetween(30, 100),
                'description' => $this->faker->randomElement([
                    'Registration fee amount in RM',
                    'Annual renewal fee amount in RM',
                ]),
            ];
        });
    }

    /**
     * Configure the factory to create an organization setting.
     */
    public function organization(): self
    {
        return $this->state(function () {
            return [
                'key' => $this->faker->randomElement([
                    'organization_name',
                    'organization_address',
                    'organization_phone',
                    'organization_email',
                ]),
                'value' => $this->faker->company,
                'description' => 'Organization information',
            ];
        });
    }
}
