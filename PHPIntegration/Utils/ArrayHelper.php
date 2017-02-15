<?php

namespace PHPIntegration\Utils;

use PHPIntegration\Utils\Interfaces\Eq;

/**
 * Bunch of useful function when working with arrays.
 */
class ArrayHelper
{
    const ARRAY_TAKE_RANDOM = 1;
    const ARRAY_TAKE_ALL = 2;
    const ARRAY_TAKE_NONE = 3;

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
     * @param bool $containment Whether to check only if $b contains $a
     * @return mixed True if objects are equal and array with path and error string otherwise.
     */
    public static function equal(array $a, array $b, array $ignored = [], bool $containment = false)
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

        if ($containment) {
            if (count($fieldsA) > count($fieldsB)) {
                return [
                    [],
                    'Second array is smaller: '
                    . count($fieldsA) . ' to ' . count($fieldsB)
                ];
            }
        } elseif (count($fieldsA) !== count($fieldsB)) {
            return [
                [],
                'Arrays have different number of elements: '
                . count($fieldsA) . ' to ' . count($fieldsB)
            ];
        }

        if (!empty(array_diff_key($fieldsA, $fieldsB))) {
            return [
                [],
                'Second array is missing keys: '
                . join(', ', array_diff(array_keys($fieldsA), array_keys($fieldsB)))
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
                $result = self::equal($value, $b[$name], [], $containment);
                if ($result !== true) {
                    array_unshift($result[0], '[' . $name . ']');
                    return $result;
                }
            } elseif (is_float($value) || is_float($b[$name])) {
                if (abs($value - $b[$name]) > 0.000001) {
                    return [
                        [$name],
                        'Value ' . $value . ' (' . gettype($value) . ') != '
                        . $b[$name] . ' (' . gettype($b[$name]) . ')'
                    ];
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

    /**
     * Function unsets array element lying in the path. There are few caveats
     * when path contains only string elements. If somewhere in the array there
     * is no current path element but the current array contains only integer
     * keys then depending on the flag argument it will:
     *   - go to random element of the current array and try unset there (ARRAY_TAKE_RANDOM)
     *   - go to every element of the current array and try unset there (ARRAY_TAKE_ALL)
     *   - fail (ARRAY_TAKE_NONE)
     * This way we can provide path, for example ["key1", 0, "key2"] and unset
     * the exact array["key1"][0]["key2"] or we can just give ["key1", "key2"] and
     * unset random second dimension of the array (for instance
     * array["key1"][12]["key2"]) or all values at second dimension.
     * @param array $path Path to unset, you may consider using PHPIntegration\Utils\TreeHelper::randomPath.
     * @param array $arr Array to unset element from.
     * @param int $flag What to do when there are no numeric key in the path
     *                  but there are only numeric keys in the array:
     *                  ARRAY_TAKE_RANDOM - try unset random element
     *                  ARRAY_TAKE_ALL - try unset every element
     *                  ARRAY_TAKE_NONE - don't unset anything
     * @return bool True when at least one element was unset and false otherwise.
     */
    public static function unsetPath(array $path, array &$arr, int $flag = self::ARRAY_TAKE_RANDOM) : bool
    {
        if (empty($path)) {
            return true;
        } elseif (empty($arr)) {
            return false;
        }

        $key = array_shift($path);

        if (array_key_exists($key, $arr)) {
            if (empty($path)) {
                unset($arr[$key]);
                return true;
            } else {
                return self::unsetPath($path, $arr[$key], $flag);
            }
        } elseif (is_string($key)
            && count(array_filter($arr, 'is_int', ARRAY_FILTER_USE_KEY)) == count($arr)) {
            switch ($flag) {
                case self::ARRAY_TAKE_RANDOM:
                    array_unshift($path, $key);
                    $idxs = array_keys($arr);
                    shuffle($idxs);
                    foreach ($idxs as $i) {
                        if (self::unsetPath($path, $arr[$i], $flag)) {
                            return true;
                        }
                    }
                    return false;
                case self::ARRAY_TAKE_ALL:
                    array_unshift($path, $key);
                    $res = false;
                    foreach ($arr as &$v) {
                        $res = self::unsetPath($path, $v, $flag) || $res;
                    }
                    return $res;
                case self::ARRAY_TAKE_NONE:
                    return false;
            }
        }

        return false;
    }

    /**
     * Function calls callback on array element lying in the path and replaces
     * it with callback return value. Same rules apply as in
     * PHPIntegration\Utils\ArrayHelper::unsetPath.
     * @param array $path Path to element to update, you may consider using
     *                    PHPIntegration\Utils\TreeHelper::randomPath.
     * @param array $arr Array to update element from.
     * @param int $flag What to do when there are no numeric key in the path
     *                  but there are only numeric keys in the array:
     *                  ARRAY_TAKE_RANDOM - try update random element
     *                  ARRAY_TAKE_ALL - try update every element
     *                  ARRAY_TAKE_NONE - don't update anything
     * @return bool True when at least one element was updated and false otherwise.
     */
    public static function updatePath(
        array $path,
        array &$arr,
        callable $callback,
        int $flag = self::ARRAY_TAKE_RANDOM
    ) : bool {
        if (empty($path)) {
            return true;
        } elseif (empty($arr)) {
            return false;
        }

        $key = array_shift($path);

        if (array_key_exists($key, $arr)) {
            if (empty($path)) {
                $arr[$key] = call_user_func($callback, $arr[$key]);
                return true;
            } else {
                return self::updatePath($path, $arr[$key], $callback, $flag);
            }
        } elseif (is_string($key)
            && count(array_filter($arr, 'is_int', ARRAY_FILTER_USE_KEY)) == count($arr)) {
            switch ($flag) {
                case self::ARRAY_TAKE_RANDOM:
                    array_unshift($path, $key);
                    $idxs = array_keys($arr);
                    shuffle($idxs);
                    foreach ($idxs as $i) {
                        if (self::updatePath($path, $arr[$i], $callback, $flag)) {
                            return true;
                        }
                    }
                    return false;
                case self::ARRAY_TAKE_ALL:
                    array_unshift($path, $key);
                    $res = false;
                    foreach ($arr as &$v) {
                        $res = self::updatePath($path, $v, $callback, $flag) || $res;
                    }
                    return $res;
                case self::ARRAY_TAKE_NONE:
                    return false;
            }
        }

        return false;
    }
}
