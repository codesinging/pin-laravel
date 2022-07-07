<?php
/**
 * Author: codesinging <codesinging@gmail.com>
 * Github: https://github.com/codesinging
 */

namespace App\Support\Reflection;

use ReflectionClass;
use ReflectionException;

class ClassReflection
{
    protected ReflectionClass $class;

    /**
     * @param string $file
     *
     * @throws ReflectionException
     */
    public function __construct(protected string $file)
    {
        $this->class = new ReflectionClass($this->file);
    }

    /**
     * 从注释中解析内容
     *
     * @param string $comment
     * @param string $prefix
     *
     * @return string|null
     */
    public function parse(string $comment, string $prefix): ?string
    {
        if (preg_match("#\*\s*{$prefix}\s+(.+)\s*\n#", $comment, $matches)) {
            return $matches[1];
        }
        return null;
    }

    /**
     * 注释中是否存在指定的内容
     *
     * @param string $comment
     * @param string $search
     *
     * @return bool|int
     */
    public function match(string $comment, string $search): bool|int
    {
        return preg_match("#\*\s*{$search}\s*\n#", $comment);
    }

    /**
     * 获取类注释
     *
     * @return bool|string
     */
    public function classComment(): bool|string
    {
        return $this->class->getDocComment();
    }

    /**
     * 获取方法注释
     *
     * @throws ReflectionException
     */
    public function methodComment(string $name): bool|string
    {
        if ($this->class->hasMethod($name)) {
            return $this->class->getMethod($name)->getDocComment();
        }
        return false;
    }
}
