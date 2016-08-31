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
        "Test1",
        "First name test",
        function ($p) {
            return "Hello " . $p["first name"]->name . "!";
        }
    ),
    new Test(
        "Test2",
        "Random name test",
        function ($p) {
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

    public static function validate(string $value, bool $valid = true)
    {
        $fstLetter = substr($value, 0, 1);
        if ($valid === true) {
            if (strtolower($fstLetter) == $fstLetter) {
                return "Value must start from upper case.\n";
            } else {
                return true;
            }
        } else {
            if (strtolower($fstLetter) == $fstLetter) {
                return true;
            } else {
                return "Value must not start from upper case.\n";
            }
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

$params = function () {
    return [
        TestParameter::objectParameter("first name", new TestObject("John")),
        TestParameter::objectParameter("random name", RandomHelper::randomObject(new TestObject(""))),
        TestParameter::objectParameter("invalid name", RandomHelper::randomObject(new TestObject(""), false), false)
    ];
};

Console::main($tests, $params);
