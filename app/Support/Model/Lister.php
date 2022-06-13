<?php
/**
 * Author: codesinging <codesinging@gmail.com>
 * Github: https://github.com/codesinging
 */

namespace App\Support\Model;

use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\Relation;

trait Lister
{
    /**
     * 获取模型列表数据
     *
     * @param Closure|Builder|Relation|null $builder
     * @param Closure|null $handler
     *
     * @return Collection|array
     */
    public function lister(Closure|Builder|Relation $builder = null, Closure $handler = null): Collection|array
    {
        $builder instanceof Closure and $builder = call_user_func($builder, $query = $this->newQuery()) ?? $query;

        $builder = $builder ?? $this->newQuery();

        $page = intval(request('page', 0));

        if ($page > 0) {
            $size = intval(request('size'));

            $pagination = $builder->paginate($size, ['*'], 'page', $page);

            if ($pagination->lastPage() < $pagination->currentPage()) {
                $pagination = $builder->paginate($size, ['*'], 'page', $pagination->lastPage());
            }

            if ($handler instanceof Closure) {
                foreach ($pagination->items() as $index => &$item) {
                    $item = $handler($item, $index);
                }
            }

            $result = [
                'page' => $pagination->currentPage(),
                'size' => $pagination->perPage(),
                'total' => $pagination->total(),
                'data' => $pagination->items(),
                'more' => $pagination->hasMorePages(),
            ];
        } else {
            $result = $builder->get();

            if ($handler instanceof Closure) {
                foreach ($result as $index => &$item) {
                    $item = $handler($item, $index);
                }
            }
        }

        return $result;
    }
}
