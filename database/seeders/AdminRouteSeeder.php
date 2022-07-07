<?php

namespace Database\Seeders;

use App\Models\AdminRoute;
use App\Support\Routing\Router;
use Illuminate\Database\Seeder;
use Illuminate\Routing\Route;
use ReflectionException;

class AdminRouteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     * @throws ReflectionException
     */
    public function run(): void
    {
        Router::routes('api/admin')->each(fn(Route $route) => AdminRoute::syncFrom($route));
    }
}
