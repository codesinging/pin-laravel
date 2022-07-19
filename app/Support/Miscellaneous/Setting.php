<?php
/**
 * Author: codesinging <codesinging@gmail.com>
 * Github: https://github.com/codesinging
 */

namespace App\Support\Miscellaneous;

use App\Models\Setting as SettingModel;
use Illuminate\Support\Collection;

class Setting
{
    /**
     * 判断设置是否存在
     *
     * @param string $key
     *
     * @return bool
     */
    public static function has(string $key): bool
    {
        return SettingModel::wheres('key', $key)->exists();
    }

    /**
     * 获取一个设置值
     *
     * @param string $key
     * @param mixed|null $default
     *
     * @return mixed
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        if (self::has($key)) {
            return SettingModel::wheres('key', $key)->value('value');
        }

        return $default;
    }

    /**
     * 获取指定或全部设值值
     *
     * @param array|null $keys
     *
     * @return Collection
     */
    public static function all(array $keys = null): Collection
    {
        if (is_null($keys)) {
            return SettingModel::instance()->pluck('value', 'key');
        }

        return SettingModel::instance()->whereIn('key', $keys)->pluck('value', 'key');
    }

    /**
     * 获取指定分组的设置值
     *
     * @param string $groupKey
     *
     * @return Collection
     */
    public static function allByGroup(string $groupKey): Collection
    {
        return SettingModel::instance()->whereRelation('group', 'key', $groupKey)->pluck('value', 'key');
    }
}
