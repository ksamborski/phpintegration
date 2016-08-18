<?php

namespace PHPIntegration\Utils;

/**
 * Bunch of useful functions when working with simple types.
 */
class ValueHelper
{
    /**
     * Returns an item when it's not empty or default value otherwise.
     * @param mixed $item Item to check
     * @param mixed $default Default value
     * @return mixed
     */
    public static function ifEmpty($item, $default)
    {
        if (empty($item)) {
            return $default;
        } else {
            return $item;
        }
    }
}
