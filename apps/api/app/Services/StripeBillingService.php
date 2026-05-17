<?php

declare(strict_types=1);

namespace App\Services;

use Stripe\Checkout\Session;
use Stripe\Stripe;

final class StripeBillingService
{
    public function __construct()
    {
        Stripe::setApiKey((string) config('services.stripe.secret'));
    }

    /**
     * Cria uma sessão real do Stripe Checkout para upgrade Pro.
     *
     * @author André Narcizo
     */
    public function createCheckoutSession(int $organizationId, string $customerEmail): Session
    {
        return Session::create([
            'mode' => 'subscription',
            'customer_email' => $customerEmail,
            'client_reference_id' => (string) $organizationId,
            'line_items' => [[
                'price' => (string) config('services.stripe.pro_price_id'),
                'quantity' => 1,
            ]],
            'success_url' => (string) config('app.frontend_url').'/billing?success=true',
            'cancel_url' => (string) config('app.frontend_url').'/billing?canceled=true',
            'metadata' => [
                'organization_id' => (string) $organizationId,
            ],
        ]);
    }
}
