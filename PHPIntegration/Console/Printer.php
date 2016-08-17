<?php

namespace PHPIntegration\Console;

class Printer
{
    public static function black($text)
    {
        return "\033[0;30m" . $text . "\033[0m";
    }

    public static function blue($text)
    {
        return "\033[0;34m" . $text . "\033[0m";
    }

    public static function green($text)
    {
        return "\033[0;32m" . $text . "\033[0m";
    }

    public static function cyan($text)
    {
        return "\033[0;36m" . $text . "\033[0m";
    }
    
    public static function red($text)
    {
        return "\033[0;31m" . $text . "\033[0m";
    }
    
    public static function purple($text)
    {
        return "\033[0;35m" . $text . "\033[0m";
    }
    
    public static function brown($text)
    {
        return "\033[0;33m" . $text . "\033[0m";
    }
    
    public static function yellow($text)
    {
        return "\033[1;33m" . $text . "\033[0m";
    }
    
    public static function white($text)
    {
        return "\033[1;37m" . $text . "\033[0m";
    }
}
