<?php

namespace PHPIntegration\Utils\Traits;

use PHPIntegration\Utils\ObjectHelper;

/**
 * Default interface \PHPIntegration\Utils\Interfaces\Eq implementation using
 * \PHPIntegration\Utils\ObjectHelper::equal
 */
trait Eq
{
    public static function equal(\PHPIntegration\Utils\Interfaces\Eq $a, \PHPIntegration\Utils\Interfaces\Eq $b)
    {
        return ObjectHelper::equal($a, $b);
    }
}
