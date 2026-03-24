<?php

namespace App\Services;

use App\Enums\AuditAction;
use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class AuditService
{
    public function log(
        AuditAction $action,
        Model $model,
        string $description,
        ?array $oldValues = null,
        ?array $newValues = null,
    ): AuditLog {
        return AuditLog::create([
            'user_id' => Auth::id(),
            'action' => $action->value,
            'auditable_type' => get_class($model),
            'auditable_id' => $model->getKey(),
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'description' => $description,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
        ]);
    }

    public function logAuth(AuditAction $action, Model $user, string $description): AuditLog
    {
        return AuditLog::create([
            'user_id' => $user->getKey(),
            'action' => $action->value,
            'auditable_type' => get_class($user),
            'auditable_id' => $user->getKey(),
            'old_values' => null,
            'new_values' => null,
            'description' => $description,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
        ]);
    }

    public function logModelCreated(Model $model): AuditLog
    {
        $description = $this->buildDescription('creó', $model);

        return $this->log(
            AuditAction::CREATED,
            $model,
            $description,
            null,
            $this->filterAttributes($model->getAttributes()),
        );
    }

    public function logModelUpdated(Model $model): AuditLog
    {
        $dirty = $model->getDirty();
        $original = array_intersect_key($model->getOriginal(), $dirty);
        $description = $this->buildDescription('editó', $model);

        return $this->log(
            AuditAction::UPDATED,
            $model,
            $description,
            $this->filterAttributes($original),
            $this->filterAttributes($dirty),
        );
    }

    public function logModelDeleted(Model $model): AuditLog
    {
        $description = $this->buildDescription('eliminó', $model);

        return $this->log(
            AuditAction::DELETED,
            $model,
            $description,
            $this->filterAttributes($model->getAttributes()),
            null,
        );
    }

    private function buildDescription(string $verb, Model $model): string
    {
        $modelName = class_basename($model);
        $userName = Auth::user()?->name ?? 'Sistema';
        $identifier = $model->getAttribute('name')
            ?? $model->getAttribute('code')
            ?? "#{$model->getKey()}";

        return "{$userName} {$verb} {$modelName} \"{$identifier}\"";
    }

    private function filterAttributes(array $attributes): array
    {
        $hidden = ['password', 'remember_token', 'two_factor_secret', 'two_factor_recovery_codes'];

        return array_diff_key($attributes, array_flip($hidden));
    }
}
