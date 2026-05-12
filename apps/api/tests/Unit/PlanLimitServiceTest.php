<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Services\PlanLimitService;
use PHPUnit\Framework\TestCase;

final class PlanLimitServiceTest extends TestCase
{
    public function testFreePlanProjectLimit(): void
    {
        $service = new PlanLimitService();

        $this->assertTrue($service->canCreateProject('free', 2));
        $this->assertFalse($service->canCreateProject('free', 3));
    }

    public function testProPlanHasUnlimitedTasks(): void
    {
        $service = new PlanLimitService();

        $this->assertTrue($service->canCreateTask('pro', 1000));
    }
}
