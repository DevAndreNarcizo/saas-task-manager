<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\StripeBillingService;
use App\Services\StripeWebhookHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Stripe\Exception\SignatureVerificationException;
use UnexpectedValueException;

final class BillingController extends Controller
{
    public function __construct(
        private readonly StripeBillingService $billing,
        private readonly StripeWebhookHandler $webhookHandler,
    ) {}

    /**
     * Inicia checkout Stripe para upgrade de organização.
     *
     * @author André Narcizo
     */
    public function checkout(Request $request, int $organization): JsonResponse
    {
        $validated = $request->validate([
            'customer_email' => ['required', 'email'],
        ]);

        $session = $this->billing->createCheckoutSession($organization, (string) $validated['customer_email']);

        return response()->json([
            'success' => true,
            'data' => ['checkout_url' => $session->url],
            'error' => null,
            'meta' => ['timestamp' => now()->toISOString()],
        ]);
    }

    /**
     * Recebe webhooks assinados do Stripe.
     *
     * @author André Narcizo
     */
    public function webhook(Request $request): JsonResponse
    {
        try {
            $type = $this->webhookHandler->handle(
                payload: $request->getContent(),
                signature: (string) $request->header('Stripe-Signature'),
            );
        } catch (UnexpectedValueException|SignatureVerificationException) {
            return response()->json([
                'success' => false,
                'data' => null,
                'error' => ['code' => 'STRIPE_WEBHOOK_INVALID', 'message' => 'Invalid Stripe webhook payload or signature.'],
                'meta' => ['timestamp' => now()->toISOString()],
            ], 400);
        }

        return response()->json([
            'success' => true,
            'data' => ['received' => true, 'type' => $type],
            'error' => null,
            'meta' => ['timestamp' => now()->toISOString()],
        ]);
    }
}
