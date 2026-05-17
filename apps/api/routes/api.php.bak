<?php

declare(strict_types=1);

use App\Http\Controllers\BillingController;
use App\Services\PlanLimitService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpFoundation\StreamedResponse;

Route::prefix('v1')->group(function (): void {
    Route::get('/plans/limits', function (): array {
        return [
            'success' => true,
            'data' => [
                'free' => ['projects' => 3, 'tasks' => 10],
                'pro' => ['projects' => null, 'tasks' => null],
            ],
            'error' => null,
            'meta' => ['timestamp' => now()->toISOString()],
        ];
    });

    Route::post('/organizations/{organization}/projects/check-limit', function (Request $request, PlanLimitService $limits): array {
        $allowed = $limits->canCreateProject(
            plan: (string) $request->input('plan', 'free'),
            currentProjects: (int) $request->input('current_projects', 0),
        );

        return [
            'success' => true,
            'data' => ['allowed' => $allowed],
            'error' => null,
            'meta' => ['timestamp' => now()->toISOString()],
        ];
    });

    Route::post('/organizations/{organization}/billing/checkout', [BillingController::class, 'checkout']);
    Route::post('/stripe/webhook', [BillingController::class, 'webhook']);

    Route::get('/events/tasks', function (): StreamedResponse {
        return response()->stream(function (): void {
            echo "event: heartbeat\n";
            echo 'data: {"status":"ok"}'."\n\n";
            flush();
        }, 200, [
            'Content-Type' => 'text/event-stream',
            'Cache-Control' => 'no-cache',
        ]);
    });
});
