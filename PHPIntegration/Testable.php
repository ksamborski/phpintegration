<?php

namespace PHPIntegration;

/**
 * Interface representing type that can be used as a test parameter.
 */
interface Testable
{
    /**
     * This method should construct new object from string given by user
     * from CLI.
     * @param string $value - string given by user
     * @return \PHPIntegration\Testable - an instance of object created from string
     */
    public static function build(string $value) : \PHPIntegration\Testable;
    
    /**
     * This method should validate string given by user from CLI before
     * building it.
     * @param string $value - string given by user
     * @param bool $valid - whether to validate for valid or invalid object
     * @return mixed - true if validation succeeded and string with useful
     *                 message otherwise
     */
    public static function validate(string $value, bool $valid = true);

    /**
     * This method should generate string representing object that user can
     * type in the CLI. It should hold the rule:
     *   $obj->build($obj->asStringParameter()) == $obj
     * @return string - string representing object that can be shown in CLI
     */
    public function asStringParameter() : string;
}
