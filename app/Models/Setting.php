<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;

class Setting extends Model
{
    protected $fillable = [
        'key',
        'value',
        'type',
        'group',
        'label',
        'description',
        'is_encrypted',
    ];

    protected $casts = [
        'is_encrypted' => 'boolean',
    ];

    /**
     * Get the value attribute with automatic decryption for encrypted settings.
     */
    protected function value(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                if ($this->is_encrypted && $value) {
                    try {
                        return Crypt::decryptString($value);
                    } catch (DecryptException $e) {
                        // Return original value if decryption fails (might be unencrypted)
                        return $value;
                    }
                }
                return $value;
            },
            set: function ($value) {
                if ($this->is_encrypted && $value) {
                    return Crypt::encryptString($value);
                }
                return $value;
            }
        );
    }

    /**
     * Get the raw (encrypted) value without decryption.
     */
    public function getRawValue(): ?string
    {
        return $this->attributes['value'] ?? null;
    }

    /**
     * Get a masked version of the value for display.
     */
    public function getMaskedValue(): ?string
    {
        $value = $this->value;

        if (!$value) {
            return null;
        }

        if ($this->is_encrypted || in_array($this->type, ['token', 'secret'])) {
            $length = strlen($value);
            if ($length <= 4) {
                return str_repeat('•', $length);
            }
            return str_repeat('•', $length - 4) . substr($value, -4);
        }

        return $value;
    }

    /**
     * Scope to get settings by group.
     */
    public function scopeByGroup($query, string $group)
    {
        return $query->where('group', $group);
    }

    /**
     * Scope to get a setting by key.
     */
    public function scopeByKey($query, string $key)
    {
        return $query->where('key', $key);
    }

    /**
     * Get all settings grouped by their group field.
     */
    public static function getAllGrouped(): array
    {
        $settings = self::all();
        $grouped = [];

        foreach ($settings as $setting) {
            if (!isset($grouped[$setting->group])) {
                $grouped[$setting->group] = [];
            }
            $grouped[$setting->group][] = [
                'id' => $setting->id,
                'key' => $setting->key,
                'value' => $setting->value,
                'masked_value' => $setting->getMaskedValue(),
                'type' => $setting->type,
                'group' => $setting->group,
                'label' => $setting->label,
                'description' => $setting->description,
                'is_encrypted' => $setting->is_encrypted,
            ];
        }

        return $grouped;
    }
}
