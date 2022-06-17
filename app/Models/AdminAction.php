<?php

namespace App\Models;

use App\Events\AdminActionCreated;
use App\Events\AdminActionDeleted;
use App\Support\Model\BaseModel;
use App\Support\Reflection\TitleReflection;
use App\Support\Routing\Router;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Routing\Route;
use ReflectionException;

/**
 * @property AdminPermission $permission
 */
class AdminAction extends BaseModel
{
    public string $guard_name = 'sanctum';

    protected $fillable = [
        'controller',
        'controller_name',
        'action',
        'action_name',
    ];

    protected $dispatchesEvents = [
        'created' => AdminActionCreated::class,
        'deleted' => AdminActionDeleted::class,
    ];

    protected $with = [
        'permission',
    ];

    public function permission(): MorphOne
    {
        return $this->morphOne(AdminPermission::class, 'permissionable');
    }

    /**
     * 根据路由同步动作
     *
     * @throws ReflectionException
     */
    public static function syncFrom(Route|string $route): Model|AdminAction|null
    {
        $router = new Router($route);
        $action = $router->action();

        $reflection = new TitleReflection($router->controller());
        $controllerName = $reflection->classTitle();
        $actionName = $reflection->methodTitle($action);

        if (!empty($controllerName) && !empty($actionName)) {
            return self::instance()->updateOrCreate([
                'controller' => $router->controller(),
                'action' => $action,
            ], [
                'controller_name' => $controllerName,
                'action_name' => $actionName,
            ]);
        }

        return null;
    }

    /**
     * 根据路由查找动作
     *
     * @param Route|string $route
     *
     * @return Model|static|null
     */
    public static function findBy(Route|string $route): Model|static|null
    {
        $router = new Router($route);

        return self::wheres('controller', $router->controller())
            ->where('action', $router->action())
            ->first();
    }
}
