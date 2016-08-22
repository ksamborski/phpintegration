<?php

require __DIR__ . '/../vendor/autoload.php';

use PHPIntegration\TestParameter;
use PHPIntegration\Test;
use PHPIntegration\Console;
use PHPIntegration\Testable;
use PHPIntegration\Utils\RandomHelper;
use PHPIntegration\Randomizable;

$tests = [
    new Test(
        "First name test",
        function($p) {
            return "Hello " . $p["first name"]->name . "!";
        }
    ),
    new Test(
        "Random name test",
        function($p) {
            return "Hello " . $p["random name"]->name . "! It can't be real name...";
        }
    )
];

class TestObject implements Randomizable, Testable
{
    public $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public static function build(string $value) : Testable
    {
        return new TestObject($value);
    }

    public static function validate(string $value)
    {
        $fstLetter = substr($value, 0, 1);
        if (strtolower($fstLetter) == $fstLetter) {
            return "Value must start from upper case.\n";
        } else {
            return true;
        }
    }

    public function asStringParameter() : string
    {
        return $this->name;
    }

    public static function randomValid()
    {
        return new TestObject(strtoupper(RandomHelper::randomString()));
    }

    public static function randomInvalid()
    {
        return new TestObject(strtolower(RandomHelper::randomString()));
    }
}

$params = function() {
    return [
        TestParameter::objectParameter("first name", new TestObject("John")),
        TestParameter::objectParameter("random name", RandomHelper::randomObject(new TestObject("")))
    ];
};

Console::main($tests, $params);
