<?php

namespace Database\Seeders;

use App\Models\AdminAction;
use App\Support\Routing\Router;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Routing\Route;
use ReflectionException;

class AdminActionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     * @throws ReflectionException
     */
    public function run(): void
    {
        Router::routes('api/admin')->each(fn(Route $route) => AdminAction::syncFrom($route));
    }
}
