<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

final class StripeWebhookTest extends TestCase
{
    use RefreshDatabase;

    public function test_checkout_completed_webhook_upgrades_organization(): void
    {
        config(['services.stripe.webhook_secret' => 'whsec_test']);
        $organizationId = DB::table('organizations')->insertGetId([
            'name' => 'Acme',
            'plan' => 'free',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $payload = json_encode([
            'id' => 'evt_test',
            'type' => 'checkout.session.completed',
            'data' => [
                'object' => [
                    'customer' => 'cus_test',
                    'subscription' => 'sub_test',
                    'metadata' => ['organization_id' => (string) $organizationId],
                ],
            ],
        ], JSON_THROW_ON_ERROR);

        $timestamp = time();
        $signature = hash_hmac('sha256', $timestamp.'.'.$payload, 'whsec_test');

        $this->call('POST', '/api/v1/stripe/webhook', [], [], [], [
            'HTTP_STRIPE_SIGNATURE' => "t={$timestamp},v1={$signature}",
            'CONTENT_TYPE' => 'application/json',
        ], $payload)
            ->assertOk()
            ->assertJsonPath('data.type', 'checkout.session.completed');

        $this->assertDatabaseHas('organizations', [
            'id' => $organizationId,
            'plan' => 'pro',
            'stripe_customer_id' => 'cus_test',
            'stripe_subscription_id' => 'sub_test',
        ]);
    }

    public function test_webhook_rejects_invalid_signature(): void
    {
        config(['services.stripe.webhook_secret' => 'whsec_test']);

        $this->postJson('/api/v1/stripe/webhook', ['type' => 'checkout.session.completed'], [
            'Stripe-Signature' => 'invalid',
        ])->assertBadRequest();
    }
}
