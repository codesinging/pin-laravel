<?php
/**
 * Author: codesinging <codesinging@gmail.com>
 * Github: https://github.com/codesinging
 */

namespace Tests\Feature\Support\Reflection;

use App\Support\Reflection\ClassReflection;
use App\Support\Reflection\ControllerReflection;
use ReflectionException;
use Tests\TestCase;

/**
 * @title 类反射测试
 */
class ControllerReflectionTest extends TestCase
{
    protected ClassReflection $reflection;

    protected function setUp(): void
    {
        $this->reflection = new ControllerReflection(self::class);
    }

    public function testControllerTitle(): void
    {
        self::assertEquals('类反射测试', $this->reflection->controllerTitle());
    }

    /**
     * @title 测试获取方法标题
     * @return void
     * @throws ReflectionException
     */
    public function testMethodTitle(): void
    {
        self::assertEquals('测试获取方法标题', $this->reflection->methodTitle('testMethodTitle'));
        self::assertNull($this->reflection->methodTitle('testClassTitle'));
    }

    public function testIsPermissionableController()
    {
        self::assertFalse($this->reflection->isPermissionableController());

        $reflection = new ControllerReflection(ClassReflectionTest::class);

        self::assertTrue($reflection->isPermissionableController());
    }

    /**
     * @title 测试是否受控方法
     * @permission
     * @return void
     * @throws ReflectionException
     */
    public function testIsPermissionableMethod(): void
    {
        self::assertFalse($this->reflection->isPermissionableMethod('testMethodTitle'));
        self::assertTrue($this->reflection->isPermissionableMethod('testIsPermissionableMethod'));
    }
}
