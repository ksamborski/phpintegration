<?php

namespace PHPIntegration\Utils;

/**
 * Bunch of useful functions when working with functions.
 */
class FunctionHelper
{
    /**
     * Returns a closure that will take an object and call it's method.
     * @param string $name Name of the method
     */
    public static function callObjMethod(string $name) : callable
    {
        return function ($obj) use ($name) {
            return $obj->$name();
        };
    }
}
