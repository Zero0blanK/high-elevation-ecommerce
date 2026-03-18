<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    protected $fillable = [
        'user_type',
        'user_id',
        'action',
        'model_type',
        'model_id',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
        'description',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    public function user()
    {
        if ($this->user_type && $this->user_id) {
            return $this->morphTo('user', 'user_type', 'user_id');
        }
        return null;
    }

    public function subject()
    {
        if ($this->model_type && $this->model_id) {
            return $this->morphTo('subject', 'model_type', 'model_id');
        }
        return null;
    }

    public function getUserNameAttribute(): string
    {
        if (!$this->user_type || !$this->user_id) {
            return 'System';
        }

        try {
            $userClass = $this->user_type;
            $user = $userClass::find($this->user_id);
            if ($user) {
                return $user->name ?? $user->first_name . ' ' . ($user->last_name ?? '');
            }
        } catch (\Exception $e) {
            // Model class may not exist
        }

        return "User #{$this->user_id}";
    }

    public function getModelLabelAttribute(): string
    {
        if (!$this->model_type) {
            return '—';
        }
        return class_basename($this->model_type) . ' #' . ($this->model_id ?? '?');
    }

    public function getChangedFieldsAttribute(): array
    {
        $old = $this->old_values ?? [];
        $new = $this->new_values ?? [];
        $fields = [];

        foreach ($new as $key => $value) {
            if (!isset($old[$key]) || $old[$key] !== $value) {
                $fields[$key] = [
                    'old' => $old[$key] ?? null,
                    'new' => $value,
                ];
            }
        }

        return $fields;
    }

    public static function log(
        string $action,
        ?string $description = null,
        ?Model $model = null,
        ?array $oldValues = null,
        ?array $newValues = null
    ): self {
        $user = auth('admin')->user() ?? auth()->user();

        return self::create([
            'user_type' => $user ? get_class($user) : null,
            'user_id' => $user?->id,
            'action' => $action,
            'model_type' => $model ? get_class($model) : null,
            'model_id' => $model?->id,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'description' => $description,
        ]);
    }
}
