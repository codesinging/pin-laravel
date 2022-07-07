<?php
/**
 * Author: codesinging <codesinging@gmail.com>
 * Github: https://github.com/codesinging
 */

namespace App\Support\Reflection;

use ReflectionException;

class ControllerReflection extends ClassReflection
{
    protected string $titleLabel = '@title';

    protected string $permissionLabel = '@permission';

    /**
     * 获取类标题
     *
     * @return string|null
     */
    public function controllerTitle(): ?string
    {
        return $this->parse($this->classComment(), $this->titleLabel);
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
            return $this->parse($comment, $this->titleLabel);
        }
        return null;
    }

    /**
     * 是否受控控制器
     *
     * @return bool
     */
    public function isPermissionableController(): bool
    {
        $comment = $this->classComment();
        $match = $this->match($comment, $this->permissionLabel);
        return is_int($match) && ($match > 0);
    }

    /**
     * 是否受控方法
     *
     * @throws ReflectionException
     */
    public function isPermissionableMethod(string $name): bool
    {
        $comment = $this->methodComment($name);
        $match = $this->match($comment, $this->permissionLabel);
        return is_int($match) && ($match > 0);
    }
}
