<?php

namespace Database\Seeders;

use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Models\Payment;
use App\Models\Receipt;
use App\Models\User;
use Illuminate\Database\Seeder;

class PaymentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create some admin users for verification purposes
        $admins = User::factory(3)
            ->create([
                'is_admin' => true,
            ]);

        // Create regular users
        $users = User::factory(10)->create();

        // Generate payments for each user
        foreach ($users as $user) {
            // Create verified payments (with receipts)
            $verifiedPayments = Payment::factory(5)
                ->verified()
                ->create([
                    'user_id' => $user->id,
                    'verified_by' => $admins->random()->id,
                ]);

            // Generate receipts for verified payments
            foreach ($verifiedPayments as $payment) {
                Receipt::factory()
                    ->forPayment($payment)
                    ->withFile()
                    ->create([
                        'generated_at' => $payment->verified_at,
                    ]);
            }

            // Create pending payments
            $pendingPayments = Payment::factory(2)
                ->pending()
                ->bankTransfer()
                ->create([
                    'user_id' => $user->id,
                ]);

            // Create some online payments
            Payment::factory(3)
                ->onlinePayment()
                ->create([
                    'user_id' => $user->id,
                    'status' => $this->faker->randomElement([
                        PaymentStatus::Verified,
                        PaymentStatus::Processing,
                    ]),
                ]);

            // Add receipts for online verified payments
            $onlineVerified = Payment::where('user_id', $user->id)
                ->where('payment_method', PaymentMethod::ChipInAsia)
                ->where('status', PaymentStatus::Verified)
                ->get();

            foreach ($onlineVerified as $payment) {
                Receipt::factory()
                    ->forPayment($payment)
                    ->create([
                        'generated_at' => $payment->verified_at ?? now(),
                    ]);
            }
        }
    }
}
