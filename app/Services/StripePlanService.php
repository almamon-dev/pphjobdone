<?php

namespace App\Services;

use App\Models\PricingPlan;
use Illuminate\Support\Facades\Log;
use Stripe\StripeClient;

class StripePlanService
{
    protected ?StripeClient $stripe = null;

    public function __construct()
    {
        $secretKey = config('services.stripe.secret') ?? env('STRIPE_SECRET');
        if ($secretKey) {
            $this->stripe = new StripeClient($secretKey);
        }
    }

    /**
     * Sync local PricingPlan with Stripe Product and Price
     */
    public function syncPlanToStripe(PricingPlan $plan): bool
    {
        if (!$this->stripe) {
            Log::warning("Stripe Secret Key missing. Skipping Stripe sync for Pricing Plan ID {$plan->id}.");
            return false;
        }

        try {
            // 1. Ensure Stripe Product exists & is updated
            if ($plan->stripe_product_id) {
                try {
                    $this->stripe->products->update($plan->stripe_product_id, [
                        'name' => $plan->name,
                        'description' => $plan->subtitle ?? 'PPHJobDone Subscription Plan',
                        'active' => (bool) $plan->status,
                    ]);
                } catch (\Exception $e) {
                    Log::warning("Existing Stripe Product {$plan->stripe_product_id} not found or failed update: " . $e->getMessage() . ". Re-creating product.");
                    $plan->stripe_product_id = null;
                }
            }

            if (!$plan->stripe_product_id) {
                $product = $this->stripe->products->create([
                    'name' => $plan->name,
                    'description' => $plan->subtitle ?? 'PPHJobDone Subscription Plan',
                    'active' => (bool) $plan->status,
                    'metadata' => [
                        'plan_id' => $plan->id,
                    ],
                ]);
                $plan->stripe_product_id = $product->id;
            }

            // 2. Parse price numeric value
            $rawPrice = preg_replace('/[^\d.]/', '', (string) $plan->price);
            $numericPrice = floatval($rawPrice);
            $unitAmount = (int) round($numericPrice * 100);
            if ($unitAmount <= 0) {
                $unitAmount = 1000; // Fallback to $10.00 if parsing fails or 0
            }

            $interval = in_array($plan->billing_interval, ['month', 'year']) ? $plan->billing_interval : 'month';

            // 3. Handle Stripe Price creation or replacement
            $createPriceNeeded = true;

            if ($plan->stripe_price_id) {
                try {
                    $existingPrice = $this->stripe->prices->retrieve($plan->stripe_price_id);
                    if (
                        $existingPrice &&
                        $existingPrice->unit_amount === $unitAmount &&
                        ($existingPrice->recurring->interval ?? '') === $interval &&
                        $existingPrice->active
                    ) {
                        $createPriceNeeded = false;
                    } else {
                        // Deactivate old price because price/interval changed
                        $this->stripe->prices->update($plan->stripe_price_id, ['active' => false]);
                    }
                } catch (\Exception $e) {
                    Log::warning("Stripe Price retrieve failed for {$plan->stripe_price_id}: " . $e->getMessage());
                }
            }

            if ($createPriceNeeded) {
                $newPrice = $this->stripe->prices->create([
                    'unit_amount' => $unitAmount,
                    'currency' => 'usd',
                    'recurring' => [
                        'interval' => $interval,
                    ],
                    'product' => $plan->stripe_product_id,
                    'active' => (bool) $plan->status,
                    'metadata' => [
                        'plan_id' => $plan->id,
                    ],
                ]);
                $plan->stripe_price_id = $newPrice->id;
            }

            $plan->save();
            return true;
        } catch (\Exception $e) {
            Log::error("Failed to sync Pricing Plan ID {$plan->id} to Stripe: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Archive/Deactivate Stripe Product and Price on deletion
     */
    public function archiveStripePlan(PricingPlan $plan): void
    {
        if (!$this->stripe) {
            return;
        }

        try {
            if ($plan->stripe_price_id) {
                $this->stripe->prices->update($plan->stripe_price_id, ['active' => false]);
            }

            if ($plan->stripe_product_id) {
                $this->stripe->products->update($plan->stripe_product_id, ['active' => false]);
            }
        } catch (\Exception $e) {
            Log::warning("Failed to archive Stripe plan ID {$plan->id}: " . $e->getMessage());
        }
    }
}
