<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Services\PlanLimitService;
use PHPUnit\Framework\TestCase;

final class PlanLimitServiceTest extends TestCase
{
    public function test_free_plan_project_limit(): void
    {
        $service = new PlanLimitService;

        $this->assertTrue($service->canCreateProject('free', 2));
        $this->assertFalse($service->canCreateProject('free', 3));
    }

    public function test_pro_plan_has_unlimited_tasks(): void
    {
        $service = new PlanLimitService;

        $this->assertTrue($service->canCreateTask('pro', 1000));
    }
}
