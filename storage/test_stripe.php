<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));

try {
    $products = \Stripe\Product::all(['limit' => 1]);
    $p = count($products->data) ? $products->data[0] : \Stripe\Product::create(['name' => 'Test']);
    
    $customers = \Stripe\Customer::all(['limit' => 1]);
    $c = count($customers->data) ? $customers->data[0] : \Stripe\Customer::create(['email' => 'test@example.com']);

    $sub = \Stripe\Subscription::create([
        'customer' => $c->id,
        'items' => [
            [
                'price_data' => [
                    'currency' => 'usd',
                    'product' => $p->id,
                    'unit_amount' => 200,
                    'recurring' => ['interval' => 'month']
                ]
            ]
        ],
        'payment_behavior' => 'default_incomplete',
        'payment_settings' => ['save_default_payment_method' => 'on_subscription'],
        'expand' => ['latest_invoice.payment_intent']
    ]);
    
    $invoice = $sub->latest_invoice;
    echo "Latest Invoice ID: " . $invoice->id . "\n";
    if (isset($invoice->payment_intent)) {
        if (is_object($invoice->payment_intent)) {
            echo "Payment Intent ID: " . $invoice->payment_intent->id . "\n";
            echo "Client Secret: " . $invoice->payment_intent->client_secret . "\n";
        } else {
            echo "Payment Intent is a string: " . $invoice->payment_intent . "\n";
        }
    } else {
        echo "Payment Intent property does NOT exist on the invoice!\n";
        echo "Invoice status: " . $invoice->status . "\n";
        echo "Invoice payment settings: " . json_encode($invoice->payment_settings) . "\n";
        echo "Invoice amount due: " . $invoice->amount_due . "\n";
    }

} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
