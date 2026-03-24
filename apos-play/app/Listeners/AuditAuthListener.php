<?php

namespace App\Listeners;

use App\Enums\AuditAction;
use App\Services\AuditService;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;

class AuditAuthListener
{
    public function __construct(protected AuditService $auditService) {}

    public function handleLogin(Login $event): void
    {
        if ($event->user) {
            $this->auditService->logAuth(
                AuditAction::LOGIN,
                $event->user,
                $event->user->name . ' inició sesión',
            );
        }
    }

    public function handleLogout(Logout $event): void
    {
        if ($event->user) {
            $this->auditService->logAuth(
                AuditAction::LOGOUT,
                $event->user,
                $event->user->name . ' cerró sesión',
            );
        }
    }
}
