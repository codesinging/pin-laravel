<?php
/**
 * Author: codesinging <codesinging@gmail.com>
 * Github: https://github.com/codesinging
 */

namespace Tests\Feature\Support\Routing;

use App\Http\Controllers\Admin\AuthController;
use App\Models\AdminRoute;
use App\Support\Routing\Router;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Routing\Route;
use Illuminate\Support\Collection;
use ReflectionException;
use Tests\TestCase;

class RouterTest extends TestCase
{
    use RefreshDatabase;

    protected string $routeAction = AuthController::class . '@login';

    protected string $routeController = AuthController::class;

    public function testRoutes()
    {
        $routes = Router::routes('api/admin');

        self::assertInstanceOf(Collection::class, $routes);

        $routes->each(fn(Route $route) => self::assertEquals('api/admin', $route->getPrefix()));
    }

    public function testController()
    {
        $router = new Router($this->routeAction);

        self::assertEquals($this->routeController, $router->controller());
    }

    public function testAction()
    {
        $router = new Router($this->routeAction);

        self::assertEquals('login', $router->action());
    }

    /**
     * @throws ReflectionException
     */
    public function testExists()
    {
        $routes = Router::routes('api/admin');

        /** @var AdminRoute $adminRoute1 */
        $adminRoute1 = AdminRoute::syncFrom(AuthController::class.'@user');

        /** @var AdminRoute $adminRoute2 */
        $adminRoute2 = AdminRoute::factory()->create();

        self::assertTrue(Router::exists($adminRoute1, $routes));
        self::assertFalse(Router::exists($adminRoute2, $routes));
    }
}
