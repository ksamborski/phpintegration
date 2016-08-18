<?php

namespace PHPIntegration\Utils;

class FunctionHelper
{
    public static function callObjMethod(string $name) : callable
    {
        return function ($obj) use ($name) {
            return $obj->$name();
        };
    }
}
