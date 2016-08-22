<?php

namespace PHPIntegration\Utils;

/**
 * Bunch of useful function when working with arrays.
 */
class ArrayHelper
{
    /**
     * Creates associative array from number ordered one. This is useful when
     * you have array of objects and you need to check if value is in the
     * array.
     *
     * @param array $a Numeric ordered array of values
     * @param callable $indexBuilder Function that takes a value from the array
     *                               and returns string which will be the key for this item.
     * @return array
     */
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

    /**
     * Checks if every element of an array satisfies a condition.
     * @param callable $checker Function that takes an item and returns bool.
     * @param array $arr Array to check
     * @return bool
     */
    public static function every(callable $checker, array $arr)
    {
        foreach ($arr as $item) {
            if (call_user_func($checker, $item) !== true) {
                return false;
            }
        }

        return true;
    }

    /**
     * Runs provided method on provided object for every item of an array.
     * @param object $object
     * @param string $methodName Name of the method which the object must have
     * @param array $items
     * @return object Return provided object
     */
    public static function onArray(object $object, string $methodName, array $items)
    {
        return array_reduce(
            $items,
            function ($carry, $item) use ($function) {
                $carry->$methodName($item);
                return $carry;
            },
            $object
        );
    }
}
