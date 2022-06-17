<?php
/**
 * Author: codesinging <codesinging@gmail.com>
 * Github: https://github.com/codesinging
 */

namespace Tests\Feature\Support\Reflection;

use App\Support\Reflection\ClassReflection;
use ReflectionException;
use Tests\TestCase;

/**
 * @title 类反射测试
 * @author 雨中歌者
 */
class ClassReflectionTest extends TestCase
{
    protected ClassReflection $reflection;

    protected function setUp(): void
    {
        $this->reflection = new ClassReflection(self::class);
    }

    public function testParse()
    {
        $comment = <<<'Comment'
    /**
     * @title title content
     * @author esinger
     *
     * @param string $comment
     * @param string $prefix
     *
     * @return string|null
     */
Comment;

        $this->assertEquals('title content', $this->reflection->parse($comment, '@title'));
        $this->assertEquals('esinger', $this->reflection->parse($comment, '@author'));
    }

    public function testClassComment()
    {
        $comment = <<<'Comment'
/**
 * @title 类反射测试
 * @author 雨中歌者
 */
Comment;

        $this->assertEquals($comment, $this->reflection->classComment());
    }

    /**
     * @title 测试获取方法注释
     * @return void
     * @throws ReflectionException
     */
    public function testMethodComment(): void
    {
        $comment = <<<'Comment'
/**
     * @title 测试获取方法注释
     * @return void
     * @throws ReflectionException
     */
Comment;

        $this->assertEquals($comment, $this->reflection->methodComment('testMethodComment'));
    }
}
