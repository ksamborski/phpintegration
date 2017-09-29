<?php

namespace PHPIntegration\Utils\Interfaces;

/**
 * Interface for checking object for equality.
 */
interface Eq
{
    /**
     * Checks if two objects are equal.
     * @param \PHPIntegration\Utils\Interfaces\Eq $a First object to test
     * @param \PHPIntegration\Utils\Interfaces\Eq $b Second object to test
     * @return mixed True if objects are equal and array with path and error string otherwise.
     */
    public static function equal(Eq $a, Eq $b);
}
