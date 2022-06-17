<?php
/**
 * Author: codesinging <codesinging@gmail.com>
 * Github: https://github.com/codesinging
 */

namespace Tests\Feature\Support\Routing;

use App\Http\Controllers\Admin\AuthController;
use App\Support\Routing\Router;
use Illuminate\Routing\Route;
use Illuminate\Support\Collection;
use Tests\TestCase;

class RouterTest extends TestCase
{
    protected string $routeAction = AuthController::class . '@login';

    protected string $routeClass = AuthController::class;

    public function testRoutes()
    {
        $routes = Router::routes('api/admin');

        self::assertInstanceOf(Collection::class, $routes);

        $routes->each(fn(Route $route) => self::assertEquals('api/admin', $route->getPrefix()));
    }

    public function testClass()
    {
        $router = new Router($this->routeAction);

        self::assertEquals($this->routeClass, $router->class());
    }

    public function testController()
    {
        $router = new Router($this->routeAction);

        self::assertEquals('Admin/AuthController', $router->controller());
    }

    public function testAction()
    {
        $router = new Router($this->routeAction);

        self::assertEquals('login', $router->action());
    }
}
