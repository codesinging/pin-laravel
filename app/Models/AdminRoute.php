<?php

namespace App\Models;

use App\Events\AdminRouteCreated;
use App\Events\AdminRouteDeleted;
use App\Events\AdminRouteUpdated;
use App\Support\Model\BaseModel;
use App\Support\Reflection\ControllerReflection;
use App\Support\Routing\Router;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Routing\Route;
use ReflectionException;

/**
 * @property AdminPermission $permission
 */
class AdminRoute extends BaseModel
{
    public string $guard_name = 'sanctum';

    protected $fillable = [
        'controller',
        'controller_name',
        'action',
        'action_name',
        'public',
    ];

    protected $casts = [
        'public' => 'boolean',
    ];

    protected $dispatchesEvents = [
        'created' => AdminRouteCreated::class,
        'updated' => AdminRouteUpdated::class,
        'deleted' => AdminRouteDeleted::class,
    ];

    public function permission(): MorphOne
    {
        return $this->morphOne(AdminPermission::class, 'permissionable');
    }

    public function logs(): HasMany
    {
        return $this->hasMany(AdminLog::class, 'route_id');
    }

    public function isPublic(): bool
    {
        return (bool)($this->attributes['public'] ?? false);
    }

    /**
     * 根据路由同步动作
     *
     * @throws ReflectionException
     */
    public static function syncFrom(Route|string $route): Model|AdminRoute|null
    {
        $router = new Router($route);
        $action = $router->action();
        $controller = $router->controller();

        $reflection = new ControllerReflection($router->controller());
        $controllerName = $reflection->controllerTitle();
        $actionName = $reflection->methodTitle($action);

        $isPermissionable = $reflection->isPermissionableController() || $reflection->isPermissionableMethod($action);

        return self::instance()->updateOrCreate([
            'controller' => $controller,
            'action' => $action,
        ], [
            'controller_name' => $controllerName ?: $controller,
            'action_name' => $actionName ?: $action,
            'public' => !$isPermissionable,
        ]);
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
