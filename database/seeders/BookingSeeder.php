<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Payment;
use App\Models\Service;
use App\Models\User;
use Illuminate\Database\Seeder;

class BookingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::where('email', '!=', 'admin@gmail.com')->get();
        $services = Service::all();
        $pricingPlans = \App\Models\PricingPlan::all();

        if ($users->isEmpty() || $services->isEmpty() || $pricingPlans->isEmpty()) {
            return;
        }

        foreach ($users as $user) {
            foreach ($services->random(2) as $service) {
                $plan = $pricingPlans->random();

                $booking = Booking::create([
                    'user_id' => $user->id,
                    'service_id' => $service->id,
                    'plan_name' => $plan->name,
                    'price' => $plan->price,
                    'status' => 'ongoing',
                    'payment_status' => 'paid',
                ]);

                Payment::create([
                    'booking_id' => $booking->id,
                    'transaction_id' => 'SEED_'.uniqid(),
                    'amount' => $plan->price,
                    'currency' => 'USD',
                    'payment_method' => 'Stripe',
                    'status' => 'paid',
                    'payment_payload' => ['seeded' => true],
                ]);
            }

            // Create one failed/pending booking
            $service = $services->random();
            $plan = $pricingPlans->random();

            Booking::create([
                'user_id' => $user->id,
                'service_id' => $service->id,
                'plan_name' => $plan->name,
                'price' => $plan->price,
                'status' => 'pending',
                'payment_status' => 'pending',
            ]);

            // Create sample SEO Audits for reports
            \App\Models\SeoAudit::create([
                'user_id' => $user->id,
                'url' => 'https://example.com',
                'response_data' => ['score' => 85, 'details' => 'Good SEO'],
                'created_at' => now()->subDays(rand(1, 30)),
            ]);

            \App\Models\SeoAudit::create([
                'user_id' => $user->id,
                'url' => 'https://mysite.io',
                'response_data' => ['score' => 92, 'details' => 'Excellent SEO'],
                'created_at' => now()->subDays(rand(31, 60)),
            ]);
        }
    }
}
