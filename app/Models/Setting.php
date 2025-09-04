<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'group',
        'key',
        'value',
    ];

    public $timestamps = false;

    /**
     * Get a setting value by group and key
     */
    public static function get($group, $key, $default = null)
    {
        $cacheKey = "setting_{$group}_{$key}";
        
        return Cache::remember($cacheKey, 3600, function () use ($group, $key, $default) {
            $setting = static::where('group', $group)
                           ->where('key', $key)
                           ->first();
            
            return $setting ? $setting->value : $default;
        });
    }

    /**
     * Set a setting value
     */
    public static function set($group, $key, $value)
    {
        $setting = static::updateOrCreate(
            ['group' => $group, 'key' => $key],
            ['value' => is_array($value) ? json_encode($value) : $value]
        );

        // Clear cache
        Cache::forget("setting_{$group}_{$key}");
        Cache::forget("settings_{$group}");

        return $setting;
    }

    /**
     * Get all settings for a group
     */
    public static function getGroup($group)
    {
        $cacheKey = "settings_{$group}";
        
        return Cache::remember($cacheKey, 3600, function () use ($group) {
            return static::where('group', $group)
                        ->pluck('value', 'key')
                        ->toArray();
        });
    }

    /**
     * Get all settings grouped by group
     */
    public static function getAllGrouped()
    {
        return static::all()
                    ->groupBy('group')
                    ->map(function ($settings) {
                        return $settings->pluck('value', 'key')->toArray();
                    })
                    ->toArray();
    }

    /**
     * Clear all settings cache
     */
    public static function clearCache()
    {
        $groups = static::distinct('group')->pluck('group');
        
        foreach ($groups as $group) {
            Cache::forget("settings_{$group}");
            
            $keys = static::where('group', $group)->pluck('key');
            foreach ($keys as $key) {
                Cache::forget("setting_{$group}_{$key}");
            }
        }
    }

    /**
     * Get value with automatic JSON decoding
     */
    public function getValueAttribute($value)
    {
        // Try to decode JSON, return original value if it fails
        $decoded = json_decode($value, true);
        return json_last_error() === JSON_ERROR_NONE ? $decoded : $value;
    }

    /**
     * Set value with automatic JSON encoding for arrays
     */
    public function setValueAttribute($value)
    {
        $this->attributes['value'] = is_array($value) ? json_encode($value) : $value;
    }

    /**
     * Boot method to clear cache when settings are updated
     */
    protected static function boot()
    {
        parent::boot();

        static::saved(function ($setting) {
            Cache::forget("setting_{$setting->group}_{$setting->key}");
            Cache::forget("settings_{$setting->group}");
        });

        static::deleted(function ($setting) {
            Cache::forget("setting_{$setting->group}_{$setting->key}");
            Cache::forget("settings_{$setting->group}");
        });
    }
}