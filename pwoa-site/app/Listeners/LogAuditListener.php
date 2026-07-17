<?php

namespace App\Listeners;

use App\Services\Shared\AuditService;

class LogAuditListener
{
    public function __construct(protected AuditService $auditService)
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(object $event): void
    {
        $action = $this->resolveActionName($event);
        $user = property_exists($event, 'user') ? $event->user : null;
        $target = property_exists($event, 'target') ? $event->target : ($user ?: null);

        if ($action) {
            $this->auditService->log($action, $target, [], [], $user);
        }
    }

    protected function resolveActionName(object $event): ?string
    {
        $className = class_basename($event);

        return match ($className) {
            'UserRegistered' => 'user.registered',
            'MembershipActivated' => 'membership.activated',
            'MembershipCancelled' => 'membership.cancelled',
            'PaymentReceived' => 'payment.received',
            'MembershipResumed' => 'membership.resumed',
            default => null,
        };
    }
}
