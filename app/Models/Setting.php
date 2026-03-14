<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = ['category', 'key', 'value', 'type', 'label', 'description', 'sort_order'];

    /**
     * Get a setting value by category and key (or "category.key").
     * Returns casted value (bool/int) when type is set.
     */
    public static function get(string $key, $default = null, ?string $category = null): mixed
    {
        if (str_contains($key, '.')) {
            [$category, $key] = explode('.', $key, 2);
        }
        if (!$category) {
            return $default;
        }
        $cacheKey = "setting.{$category}.{$key}";
        $value = Cache::remember($cacheKey, 3600, function () use ($category, $key) {
            $row = static::where('category', $category)->where('key', $key)->first();
            return $row ? $row->value : null;
        });
        if ($value === null) {
            $def = static::getDefinition($category, $key);
            $value = $def['default'] ?? $default;
        }
        return static::castValue($value, static::getType($category, $key));
    }

    protected static function getType(string $category, string $key): string
    {
        $def = static::getDefinition($category, $key);
        return $def['type'] ?? 'text';
    }

    protected static function castValue(mixed $value, string $type): mixed
    {
        if ($value === null) {
            return null;
        }
        return match ($type) {
            'boolean' => filter_var($value, FILTER_VALIDATE_BOOLEAN),
            'integer' => (int) $value,
            default => (string) $value,
        };
    }

    /**
     * Get definition from config for a key (label, type, default).
     */
    public static function getDefinition(string $category, string $key): array
    {
        $categories = config('settings.categories', []);
        $cat = $categories[$category] ?? null;
        if (!$cat || empty($cat['keys'][$key])) {
            return ['type' => 'text', 'label' => $key, 'default' => null];
        }
        $def = $cat['keys'][$key];
        return [
            'type' => $def['type'] ?? 'text',
            'label' => $def['label'] ?? $key,
            'description' => $def['description'] ?? null,
            'default' => $def['default'] ?? null,
        ];
    }

    /**
     * Get all definitions for a category (keys with label, type, default).
     */
    public static function getDefinitionsForCategory(string $category): array
    {
        $categories = config('settings.categories', []);
        $cat = $categories[$category] ?? null;
        if (!$cat || empty($cat['keys'])) {
            return [];
        }
        return $cat['keys'];
    }

    /**
     * Get all category slugs and labels for settings UI.
     */
    public static function getCategories(): array
    {
        $categories = config('settings.categories', []);
        $result = [];
        foreach ($categories as $slug => $config) {
            $result[$slug] = [
                'label' => $config['label'] ?? ucfirst($slug),
                'icon' => $config['icon'] ?? 'fas fa-cog',
            ];
        }
        return $result;
    }

    /**
     * Set a setting value. Clears cache for that key.
     */
    public static function set(string $category, string $key, mixed $value): void
    {
        $value = is_bool($value) ? ($value ? '1' : '0') : (string) $value;
        static::updateOrCreate(
            ['category' => $category, 'key' => $key],
            ['value' => $value, 'type' => static::getType($category, $key)]
        );
        Cache::forget("setting.{$category}.{$key}");
    }

    /**
     * Get all settings for a category as key => value (casted).
     */
    public static function getByCategory(string $category): array
    {
        $defs = static::getDefinitionsForCategory($category);
        $rows = static::where('category', $category)->get()->keyBy('key');
        $out = [];
        foreach ($defs as $key => $def) {
            $row = $rows->get($key);
            $value = $row ? $row->value : ($def['default'] ?? null);
            $out[$key] = static::castValue($value, $def['type'] ?? 'text');
        }
        return $out;
    }

    /**
     * Seed default settings from config (insert missing only).
     */
    public static function seedFromConfig(): void
    {
        $categories = config('settings.categories', []);
        $order = 0;
        foreach ($categories as $category => $config) {
            foreach ($config['keys'] ?? [] as $key => $def) {
                static::firstOrCreate(
                    ['category' => $category, 'key' => $key],
                    [
                        'value' => $def['default'] ?? null,
                        'type' => $def['type'] ?? 'text',
                        'label' => $def['label'] ?? $key,
                        'description' => $def['description'] ?? null,
                        'sort_order' => $order++,
                    ]
                );
            }
        }
        Cache::flush();
    }
}
