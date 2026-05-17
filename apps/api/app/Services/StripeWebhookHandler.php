<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Stripe\Webhook;
use UnexpectedValueException;

final class StripeWebhookHandler
{
    /**
     * Processa um webhook assinado do Stripe.
     *
     * @return string O tipo do evento processado.
     *
     * @throws UnexpectedValueException Se o payload ou secret forem inválidos.
     */
    public function handle(string $payload, string $signature): string
    {
        $secret = (string) config('services.stripe.webhook_secret');
        if (empty($secret)) {
            Log::error('STRIPE_WEBHOOK_SECRET não configurado');
            throw new UnexpectedValueException('Server configuration error');
        }

        $event = Webhook::constructEvent(
            $payload,
            $signature,
            $secret,
        );

        $type = $event->type;
        $object = $event->data->object;
        $eventId = $event->id;

        // Idempotency check: skip if this event was already processed
        if (DB::table('organizations')->where('stripe_event_id', $eventId)->exists()) {
            return $type;
        }

        if ($type === 'checkout.session.completed' && isset($object->metadata['organization_id'])) {
            $organizationId = (int) $object->metadata['organization_id'];

            DB::transaction(function () use ($organizationId, $object, $eventId): void {
                DB::table('organizations')->where('id', $organizationId)->update([
                    'plan' => 'pro',
                    'stripe_customer_id' => $object->customer,
                    'stripe_subscription_id' => $object->subscription,
                    'stripe_event_id' => $eventId,
                    'updated_at' => now(),
                ]);
            });
        }

        if (in_array($type, ['customer.subscription.deleted', 'customer.subscription.paused'], true) && isset($object->id)) {
            DB::transaction(function () use ($object, $eventId): void {
                DB::table('organizations')->where('stripe_subscription_id', (string) $object->id)->update([
                    'plan' => 'free',
                    'stripe_event_id' => $eventId,
                    'updated_at' => now(),
                ]);
            });
        }

        if (! is_string($type) || $type === '') {
            throw new UnexpectedValueException('Invalid Stripe event type.');
        }

        return $type;
    }
}
