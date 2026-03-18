<?php

namespace App\Traits;

use App\Models\AuditLog;

trait Auditable
{
    public static function bootAuditable(): void
    {
        static::created(function ($model) {
            AuditLog::log(
                'created',
                class_basename($model) . ' created',
                $model,
                null,
                $model->getAttributes()
            );
        });

        static::updated(function ($model) {
            $dirty = $model->getDirty();
            if (empty($dirty)) {
                return;
            }

            $oldValues = [];
            foreach (array_keys($dirty) as $key) {
                $oldValues[$key] = $model->getOriginal($key);
            }

            AuditLog::log(
                'updated',
                class_basename($model) . ' updated',
                $model,
                $oldValues,
                $dirty
            );
        });

        static::deleted(function ($model) {
            AuditLog::log(
                'deleted',
                class_basename($model) . ' deleted',
                $model,
                $model->getAttributes(),
                null
            );
        });
    }
}
