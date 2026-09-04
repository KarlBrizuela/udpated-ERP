<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentSetting extends Model
{
    use HasFactory;

    protected $fillable = ['key', 'value'];

    protected $casts = [
        'value' => 'array'
    ];

    /**
     * Get a specific payment setting by key
     */
    public static function getSetting($key, $default = null)
    {
        $setting = self::where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }

    /**
     * Set a specific payment setting
     */
    public static function setSetting($key, $value)
    {
        return self::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );
    }

    /**
     * Get all payment settings as an array
     */
    public static function getAllSettings()
    {
        return self::all()->pluck('value', 'key')->toArray();
    }
}
