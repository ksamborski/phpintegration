<?php

namespace PHPIntegration\Console;

/**
 * Bunch of useful console printing functions.
 */
class Printer
{
    /**
     * Returns a text that will be printed in black in the console.
     * @param string $text Text to print.
     * @return string Encoded string recognized by the terminal.
     */
    public static function black($text)
    {
        return "\033[0;30m" . $text . "\033[0m";
    }

    /**
     * Returns a text that will be printed in blue in the console.
     * @param string $text Text to print.
     * @return string Encoded string recognized by the terminal.
     */
    public static function blue($text)
    {
        return "\033[0;34m" . $text . "\033[0m";
    }

    /**
     * Returns a text that will be printed in green in the console.
     * @param string $text Text to print.
     * @return string Encoded string recognized by the terminal.
     */
    public static function green($text)
    {
        return "\033[0;32m" . $text . "\033[0m";
    }

    /**
     * Returns a text that will be printed in cyan in the console.
     * @param string $text Text to print.
     * @return string Encoded string recognized by the terminal.
     */
    public static function cyan($text)
    {
        return "\033[0;36m" . $text . "\033[0m";
    }
    
    /**
     * Returns a text that will be printed in red in the console.
     * @param string $text Text to print.
     * @return string Encoded string recognized by the terminal.
     */
    public static function red($text)
    {
        return "\033[0;31m" . $text . "\033[0m";
    }
    
    /**
     * Returns a text that will be printed in purple in the console.
     * @param string $text Text to print.
     * @return string Encoded string recognized by the terminal.
     */
    public static function purple($text)
    {
        return "\033[0;35m" . $text . "\033[0m";
    }
    
    /**
     * Returns a text that will be printed in brown in the console.
     * @param string $text Text to print.
     * @return string Encoded string recognized by the terminal.
     */
    public static function brown($text)
    {
        return "\033[0;33m" . $text . "\033[0m";
    }
    
    /**
     * Returns a text that will be printed in yellow in the console.
     * @param string $text Text to print.
     * @return string Encoded string recognized by the terminal.
     */
    public static function yellow($text)
    {
        return "\033[1;33m" . $text . "\033[0m";
    }
    
    /**
     * Returns a text that will be printed in white in the console.
     * @param string $text Text to print.
     * @return string Encoded string recognized by the terminal.
     */
    public static function white($text)
    {
        return "\033[1;37m" . $text . "\033[0m";
    }

    /**
     * Returns a text that will list all values from the given array.
     * @param array $vals Values to list
     * @return string Encoded string recognized by the terminal.
     */
    public static function listValues(array $vals) : string
    {
        return array_reduce(
            $vals,
            function ($res, $val) {
                return $res . Printer::green("- ") . $val . "\n";
            },
            ""
        );
    }
}
