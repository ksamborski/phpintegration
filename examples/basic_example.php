<?php

require __DIR__ . '/../vendor/autoload.php';

use PHPIntegration\TestParameter;
use PHPIntegration\Test;
use PHPIntegration\Console;

$tests = [
    new Test(
        "Simple test 1",
        function ($p) {
            usleep(rand(10000, 100000));
            return true;
        }
    ),
    new Test(
        "Failing test",
        function ($p) {
            return "this is a test that always fails";
        }
    )
];

$params = function () {
    return [
        TestParameter::manyFromParameter("departments", ["Warsaw", "Berlin"], ["Warsaw", "Berlin", "Cracow"]),
        TestParameter::stringParameter("currency", "PLN"),
        TestParameter::regexParameter("date", "2015-01-01", "/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/"),
        TestParameter::arrayOfParameter("hours", [12], '\PHPIntegration\TestParameter::intParameter')
    ];
};

Console::main($tests, $params);
