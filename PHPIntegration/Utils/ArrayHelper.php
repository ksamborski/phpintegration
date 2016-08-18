<?php

namespace PHPIntegration\Utils;

class ArrayHelper
{
    public static function associative(array $a, callable $indexBuilder)
    {
        return array_reduce(
            $a,
            function ($arr, $item) use ($indexBuilder) {
                $arr[call_user_func($indexBuilder, $item)] = $item;
                return $arr;
            },
            []
        );
    }

    public static function every(callable $checker, array $arr)
    {
        foreach ($arr as $item) {
            if (call_user_func($checker, $item) !== true) {
                return false;
            }
        }

        return true;
    }
}
