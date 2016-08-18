<?php

namespace PHPIntegration\Utils;

class ValueHelper
{
    public static function ifEmpty($item, $default)
    {
        if (empty($item)) {
            return $default;
        } else {
            return $item;
        }
    }
}
