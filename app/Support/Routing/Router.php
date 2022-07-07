<?php
/**
 * Author: codesinging <codesinging@gmail.com>
 * Github: https://github.com/codesinging
 */

namespace App\Support\Routing;

use App\Models\AdminRoute;
use Illuminate\Routing\Route;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Route as RouteFacade;

class Router
{
    protected Route $route;

    protected ?string $controller;
    protected ?string $action;

    public function __construct(Route|string $route)
    {
        $this->route = is_string($route) ? RouteFacade::getRoutes()->getByAction($route) : $route;
        $this->parseRoute();
    }

    private function parseRoute(): void
    {
        list($this->controller, $this->action) = explode('@', $this->route->getActionName());
    }

    /**
     * 获取全部或指定前缀的路由集合
     *
     * @param string|null $prefix
     *
     * @return Collection
     */
    public static function routes(string $prefix = null): Collection
    {
        $routes = collect(RouteFacade::getRoutes()->getRoutes());

        return $prefix ? $routes->filter(fn(Route $route) => $route->getPrefix() === $prefix) : $routes;
    }

    /**
     * 判断指定的路由模型是否存在于指定的路由集合中
     *
     * @param AdminRoute $adminRoute
     * @param Collection $routes
     *
     * @return bool
     */
    public static function exists(AdminRoute $adminRoute, Collection $routes): bool
    {
        return $routes->contains(function (Route $route) use ($adminRoute) {
            $router = new static($route);

            return $router->controller() === $adminRoute['controller'] && $router->action() === $adminRoute['action'];
        });
    }

    /**
     * 返回路由的控制器名
     *
     * @return string|null
     */
    public function controller(): ?string
    {
        return $this->controller;
    }

    /**
     * 返回路由的动作名
     *
     * @return string|null
     */
    public function action(): ?string
    {
        return $this->action;
    }
}
