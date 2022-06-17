<?php
/**
 * Author: codesinging <codesinging@gmail.com>
 * Github: https://github.com/codesinging
 */

namespace App\Support\Reflection;

use ReflectionException;

class TitleReflection extends ClassReflection
{
    protected string $prefix = '@title';

    /**
     * 获取类标题
     *
     * @return string|null
     */
    public function classTitle(): ?string
    {
        return $this->parse($this->classComment(), $this->prefix);
    }

    /**
     * 获取方法标题
     *
     * @param string $name
     *
     * @return string|null
     * @throws ReflectionException
     */
    public function methodTitle(string $name): ?string
    {
        if ($comment = $this->methodComment($name)) {
            return $this->parse($comment, $this->prefix);
        }
        return null;
    }
}
