<?php
/**
 * Author: codesinging <codesinging@gmail.com>
 * Github: https://github.com/codesinging
 */

namespace App\Support\Model;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

trait StaticMethods
{
    /**
     * 创建一个模型
     *
     * @param array $attributes
     *
     * @return Model|static
     */
    public static function creates(array $attributes = []): Model|static
    {
        return (new static())->create($attributes);
    }

    /**
     * 查找第一个模型
     *
     * @param array|string $columns
     *
     * @return Model|static|null
     */
    public static function firsts(array|string $columns = ['*']): Model|static|null
    {
        return (new static())->first($columns);
    }

    /**
     * 查找指定主键的模型
     *
     * @param mixed $id
     * @param array|string $columns
     *
     * @return Model|Collection|static[]|static|null
     */
    public static function finds(mixed $id, array|string $columns = ['*']): Model|Collection|array|static|null
    {
        return (new static())->find($id, $columns);
    }
}
