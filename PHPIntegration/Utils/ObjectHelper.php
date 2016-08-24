<?php

namespace PHPIntegration\Utils;

use PHPIntegration\Utils\Interfaces\Eq;

/**
 * Bunch of useful functions when working with objects..
 */
class ObjectHelper
{
    /**
     * Checks if each property of the first object exists and has the same
     * value in the second one. If some property is an array it will execute
     * \PHPIntegration\Utils\ArrayHelper::equal for comparision. And if some is
     * an object it will recur unless the property is an instance of
     * \PHPIntegration\Utils\Interfaces\Eq, then it will call equal method from
     * the interface.
     * @param object $a First object to test
     * @param object $b Second object to test
     * @param array $ignored Properties names to skip
     * @return mixed True if objects are equal and array with path and error string otherwise.
     */
    public static function equal($a, $b, array $ignored = [])
    {
        if (get_class($a) != get_class($b)) {
            return [ [], 'Diffrent objects ' . get_class($a) . ' - ' . get_class($b) ];
        }

        $fields = array_filter(
            get_object_vars($a),
            function ($key) use ($ignored) {
                return !in_array($key, $ignored);
            },
            ARRAY_FILTER_USE_KEY
        );

        foreach ($fields as $name => $value) {
            if ($value instanceof Eq && $b->{$name} instanceof Eq) {
                $result = $value->equal($value, $b->{$name});
                if ($result !== true) {
                    if (is_array($result)) {
                        array_unshift($result[0], $name);
                        return $result;
                    } else {
                        return [ [$name], 'Objects aren\'t equal' ];
                    }
                }
            } elseif (is_object($value) && is_object($b->{$name})) {
                $result = self::equal($value, $b->{$name});
                if ($result !== true) {
                    array_unshift($result[0], $name);
                    return $result;
                }
            } elseif (is_array($value) && is_array($b->{$name})) {
                $result = ArrayHelper::equal($value, $b->{$name});
                if ($result !== true) {
                    array_unshift($result[0], $name);
                    return $result;
                }
            } elseif (is_float($value) || is_float($b->{$name})) {
                if (abs($value - $b->{$name}) > 0.000001) {
                    return [
                        [$name],
                        'Value ' . $value . ' (' . gettype($value) . ') != '
                        . $b->{$name} . ' (' . gettype($b->{$name}) . ')'
                    ];
                }
            } elseif ($value !== $b->{$name}) {
                return [
                    [$name],
                    'Value ' . var_export($value, true) . ' (' . gettype($value) . ') != '
                    . var_export($b->{$name}, true) . ' (' . gettype($b->{$name}) . ')'
                ];
            }
        }
        return true;
    }
}
