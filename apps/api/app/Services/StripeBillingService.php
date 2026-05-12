<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Stripe\Checkout\Session;
use Stripe\Stripe;
use Stripe\Webhook;
use UnexpectedValueException;

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

    /**
     * Valida assinatura do Stripe e sincroniza assinatura local.
     *
     * @author André Narcizo
     */
    public function handleWebhook(string $payload, string $signature): string
    {
        $event = Webhook::constructEvent(
            $payload,
            $signature,
            (string) config('services.stripe.webhook_secret'),
        );

        $type = $event->type;
        $object = $event->data->object;

        if ($type === 'checkout.session.completed' && isset($object->metadata->organization_id)) {
            DB::table('organizations')->where('id', (int) $object->metadata->organization_id)->update([
                'plan' => 'pro',
                'stripe_customer_id' => $object->customer,
                'stripe_subscription_id' => $object->subscription,
                'updated_at' => now(),
            ]);
        }

        if (in_array($type, ['customer.subscription.deleted', 'customer.subscription.paused'], true) && isset($object->id)) {
            DB::table('organizations')->where('stripe_subscription_id', (string) $object->id)->update([
                'plan' => 'free',
                'updated_at' => now(),
            ]);
        }

        if (! is_string($type) || $type === '') {
            throw new UnexpectedValueException('Invalid Stripe event type.');
        }

        return $type;
    }
}
