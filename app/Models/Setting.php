<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class Setting extends Model
{
    protected $fillable = [
        'key',
        'value',
        'type',
        'group',
        'label',
        'description',
    ];

    /**
     * Get a setting value by key.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        $setting = self::where('key', $key)->first();
        
        if (!$setting) {
            return $default;
        }
        
        return self::castValue($setting->value, $setting->type);
    }

    /**
     * Set a setting value.
     */
    public static function set(string $key, mixed $value): void
    {
        $setting = self::where('key', $key)->first();
        
        if ($setting) {
            // Encrypt if type is encrypted
            if ($setting->type === 'encrypted' && $value) {
                $value = Crypt::encryptString($value);
            }
            
            $setting->update(['value' => $value]);
        }
    }

    /**
     * Get all settings as array.
     */
    public static function getAll(): array
    {
        return self::all()->keyBy('key')->map(function ($setting) {
            return [
                'value' => $setting->value,
                'type' => $setting->type,
                'group' => $setting->group,
            ];
        })->toArray();
    }

    /**
     * Get settings by group.
     */
    public static function getByGroup(string $group): array
    {
        return self::where('group', $group)
            ->get()
            ->mapWithKeys(function ($setting) {
                return [$setting->key => self::castValue($setting->value, $setting->type)];
            })
            ->toArray();
    }

    /**
     * Cast value based on type.
     */
    protected static function castValue(mixed $value, string $type): mixed
    {
        if ($value === null) {
            return null;
        }

        return match ($type) {
            'boolean' => filter_var($value, FILTER_VALIDATE_BOOLEAN),
            'integer' => (int) $value,
            'encrypted' => self::decryptValue($value),
            default => $value,
        };
    }

    /**
     * Decrypt encrypted value.
     */
    protected static function decryptValue(string $value): string
    {
        try {
            return Crypt::decryptString($value);
        } catch (\Exception) {
            return $value;
        }
    }
}
