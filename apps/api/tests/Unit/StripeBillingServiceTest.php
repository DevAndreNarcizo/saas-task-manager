<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Services\StripeBillingService;
use Mockery;
use Stripe\Checkout\Session;

/**
 * Testa o StripeBillingService com mocks.
 *
 * Nota: Usamos @runInSeparateProcess + @preserveGlobalState para permitir
 * que Mockery faça overload das classes do Stripe sem conflitos entre testes.
 */
final class StripeBillingServiceTest extends \Tests\TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['services.stripe.secret' => 'sk_test_mock']);
        config(['services.stripe.pro_price_id' => 'price_mock_pro']);
        config(['app.frontend_url' => 'http://localhost:3000']);
    }

    /**
     * Testa createCheckoutSession com dados válidos.
     *
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function test_create_checkout_session_with_valid_data(): void
    {
        // Mock da classe Session via overload (antes de ser carregada)
        $sessionMock = Mockery::mock('overload:' . Session::class);
        $sessionMock->shouldReceive('create')
            ->once()
            ->with(Mockery::on(function (array $params): bool {
                return $params['mode'] === 'subscription'
                    && $params['customer_email'] === 'user@example.com'
                    && $params['client_reference_id'] === '1'
                    && $params['line_items'][0]['price'] === 'price_mock_pro'
                    && $params['metadata']['organization_id'] === '1';
            }))
            ->andReturn($sessionMock);

        $sessionMock->url = 'https://checkout.stripe.com/pay/mock_session';

        $service = new StripeBillingService();
        $session = $service->createCheckoutSession(1, 'user@example.com');

        $this->assertSame('https://checkout.stripe.com/pay/mock_session', $session->url);
    }

    /**
     * Testa createCheckoutSession com dados inválidos.
     *
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function test_create_checkout_session_throws_exception_with_invalid_data(): void
    {
        $sessionMock = Mockery::mock('overload:' . Session::class);
        $sessionMock->shouldReceive('create')
            ->once()
            ->andThrow(new \Stripe\Exception\InvalidRequestException('Invalid parameters'));

        $service = new StripeBillingService();

        $this->expectException(\Stripe\Exception\InvalidRequestException::class);
        $service->createCheckoutSession(0, '');
    }

    /**
     * Testa que a Stripe API é chamada com os parâmetros corretos.
     *
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function test_create_checkout_session_calls_stripe_api_correctly(): void
    {
        $sessionMock = Mockery::mock('overload:' . Session::class);
        $sessionMock->shouldReceive('create')
            ->once()
            ->with(Mockery::on(function (array $params): bool {
                $requiredKeys = ['mode', 'customer_email', 'client_reference_id', 'line_items', 'success_url', 'cancel_url', 'metadata'];
                foreach ($requiredKeys as $key) {
                    if (! array_key_exists($key, $params)) {
                        return false;
                    }
                }
                if (! isset($params['line_items'][0]['price']) || ! isset($params['line_items'][0]['quantity'])) {
                    return false;
                }
                return true;
            }))
            ->andReturn($sessionMock);

        $sessionMock->url = 'https://checkout.stripe.com/pay/42';

        $service = new StripeBillingService();
        $session = $service->createCheckoutSession(42, 'org@test.com');

        $this->assertNotNull($session->url);
        $this->assertStringContainsString('42', $session->url);
    }
}
