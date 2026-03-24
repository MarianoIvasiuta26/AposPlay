<?php

namespace App\Traits;

use App\Services\AuditService;

trait Auditable
{
    public static function bootAuditable(): void
    {
        static::created(function ($model) {
            app(AuditService::class)->logModelCreated($model);
        });

        static::updated(function ($model) {
            if ($model->wasChanged()) {
                app(AuditService::class)->logModelUpdated($model);
            }
        });

        static::deleted(function ($model) {
            app(AuditService::class)->logModelDeleted($model);
        });
    }
}
