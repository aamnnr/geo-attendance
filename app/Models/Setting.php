<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

/**
 * Pengaturan key-value aplikasi. Nilai default ada di config/attendance.php.
 */
class Setting extends Model
{
    private const CACHE_KEY = 'app.settings';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $primaryKey = 'key';

    protected $fillable = ['key', 'value'];

    /**
     * Semua pengaturan: default dari config ditimpa nilai yang tersimpan di database.
     *
     * @return array<string, mixed>
     */
    public static function values(): array
    {
        $stored = Cache::rememberForever(self::CACHE_KEY, fn () => static::query()->pluck('value', 'key')->all());

        return array_merge(config('attendance.defaults'), $stored);
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        return static::values()[$key] ?? $default;
    }

    /** @param  array<string, mixed>  $values */
    public static function put(array $values): void
    {
        foreach ($values as $key => $value) {
            static::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        Cache::forget(self::CACHE_KEY);
    }
}
