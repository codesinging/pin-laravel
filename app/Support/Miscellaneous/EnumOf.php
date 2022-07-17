<?php
/**
 * Author: codesinging <codesinging@gmail.com>
 * Github: https://github.com/codesinging
 */

namespace App\Support\Miscellaneous;

use BackedEnum;
use Illuminate\Support\Arr;

trait EnumOf
{
    public static function of(string $name): static|null
    {
        return Arr::first(self::cases(), function (BackedEnum $type) use ($name) {
            return $type->name === $name;
        });
    }
}
