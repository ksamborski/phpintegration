<?php

namespace PHPIntegration;

/**
 * Interface representing type that can be random generated.
 */
interface Randomizable
{
    /**
     * This method should generate class instance with valid properties values.
     */
    public static function randomValid();

    /**
     * This method should generate class instance with invalid properties values.
     */
    public static function randomInvalid();
}
