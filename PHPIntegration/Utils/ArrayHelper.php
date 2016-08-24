<?php

namespace PHPIntegration\Utils;

use PHPIntegration\Utils\Interfaces\Eq;

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

    /**
     * Checks if two arrays are the same. If some value is an
     * object it will execute \PHPIntegration\Utils\ObjectHelper::equal for
     * comparision unless it is an instance of
     * \PHPIntegration\Utils\Interfaces\Eq, then it will call equal method from
     * the interface. And if some is an array it will recur. 
     * @param array $a First object to test
     * @param array $b Second object to test
     * @param array $ignored Properties names to skip
     * @return mixed True if objects are equal and array with path and error string otherwise.
     */
    public static function equal(array $a, array $b, array $ignored = [])
    {
        $fieldsA = array_filter(
            $a,
            function ($key) use ($ignored) {
                return !in_array($key, $ignored);
            },
            ARRAY_FILTER_USE_KEY
        );
        $fieldsB = array_filter(
            $b,
            function ($key) use ($ignored) {
                return !in_array($key, $ignored);
            },
            ARRAY_FILTER_USE_KEY
        );

        if (count($fieldsA) !== count($fieldsB)) {
            return [
                [],
                'Arrays have different number of elements: '
                . $count($fieldsA) . ' to ' . count($fieldsB)
            ];
        } elseif (!empty(array_diff_key($fieldsA, $fieldsB))) {
            return [
                [],
                'Second array is missing keys: '
                . join(', ', array_diff_key($fieldsA, $fieldsB))
            ];
        }

        foreach ($fieldsA as $name => $value) {
            if ($value instanceof Eq && $b[$name] instanceof Eq) {
                $result = $value->equal($value, $b[$name]);
                if ($result !== true) {
                    if (is_array($result)) {
                        array_unshift($result[0], '[' . $name . ']');
                        return $result;
                    } else {
                        return [ ['[' . $name . ']'], 'Objects aren\'t equal' ];
                    }
                }
            } elseif (is_object($value) && is_object($b[$name])) {
                $result = ObjectHelper::equal($value, $b[$name]);
                if ($result !== true) {
                    array_unshift($result[0], '[' . $name . ']');
                    return $result;
                }
            } elseif (is_array($value) && is_array($b[$name])) {
                $result = self::equal($value, $b[$name]);
                if ($result !== true) {
                    array_unshift($result[0], '[' . $name . ']');
                    return $result;
                }
            } elseif ($value !== $b[$name]) {
                return [
                    ['[' . $name . ']'],
                    'Value ' . var_export($value, true) . ' (' . gettype($value) . ') != '
                    . var_export($b[$name], true) . ' (' . gettype($b[$name]) . ')'
                ];
            }
        }
        return true;
    }
}
