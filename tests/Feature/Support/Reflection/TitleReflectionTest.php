<?php
/**
 * Author: codesinging <codesinging@gmail.com>
 * Github: https://github.com/codesinging
 */

namespace Tests\Feature\Support\Reflection;

use App\Support\Reflection\ClassReflection;
use App\Support\Reflection\TitleReflection;
use ReflectionException;
use Tests\TestCase;

/**
 * @title 类反射测试
 */
class TitleReflectionTest extends TestCase
{
    protected ClassReflection $reflection;

    protected function setUp(): void
    {
        $this->reflection = new TitleReflection(self::class);
    }

    public function testClassTitle(): void
    {
        self::assertEquals('类反射测试', $this->reflection->classTitle());
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
}
