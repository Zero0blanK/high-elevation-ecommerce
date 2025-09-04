<?php

namespace App\Traits;

trait DynamicSoftDeletes
{
    protected function shouldUseSoftDeletes(): bool
    {
        // Check if SoftDeletes trait is being used
        if (!in_array('Illuminate\Database\Eloquent\SoftDeletes', class_uses_recursive($this))) {
            return false;
        }

        $globalEnabled = config('soft_deletes.enabled', true);
        $modelKey = strtolower(class_basename($this));
        $modelEnabled = config("soft_deletes.models.{$modelKey}", true);
        
        return $globalEnabled && $modelEnabled;
    }

    public function smartDelete()
    {
        if ($this->shouldUseSoftDeletes()) {
            // Use the parent delete method from SoftDeletes
            return parent::delete();
        }
        
        // Perform hard delete
        return $this->performHardDelete();
    }

    public function smartRestore()
    {
        if ($this->shouldUseSoftDeletes() && method_exists($this, 'restore')) {
            return $this->restore();
        }
        
        return false;
    }

    public function isSmartTrashed()
    {
        if (!$this->shouldUseSoftDeletes()) {
            return false;
        }
        
        return method_exists($this, 'trashed') ? $this->trashed() : false;
    }

    protected function performHardDelete()
    {
        if ($this->fireModelEvent('deleting') === false) {
            return false;
        }

        $this->exists = false;

        $result = $this->newModelQuery()->where($this->getKeyName(), $this->getKey())->delete();

        $this->fireModelEvent('deleted', false);

        return $result;
    }

    protected static function bootDynamicSoftDeletes()
    {
        // Only apply soft delete scope if enabled
        $instance = new static;
        if ($instance->shouldUseSoftDeletes()) {
            static::addGlobalScope(new \Illuminate\Database\Eloquent\SoftDeletingScope);
        }
    }
}