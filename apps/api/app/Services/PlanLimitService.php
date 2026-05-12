<?php

declare(strict_types=1);

namespace App\Services;

final class PlanLimitService
{
    /**
     * Verifica se uma organização pode criar mais projetos.
     *
     * @author André Narcizo
     */
    public function canCreateProject(string $plan, int $currentProjects): bool
    {
        return $plan === 'pro' || $currentProjects < 3;
    }

    /**
     * Verifica se uma organização pode criar mais tarefas.
     *
     * @author André Narcizo
     */
    public function canCreateTask(string $plan, int $currentTasks): bool
    {
        return $plan === 'pro' || $currentTasks < 10;
    }
}
