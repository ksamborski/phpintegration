<?php

require __DIR__ . '/../vendor/autoload.php';

use PHPIntegration\TestParameter;
use PHPIntegration\Test;
use PHPIntegration\Console;

$tests = [
    new Test(
        "Simple test 1",
        function($p) {
            return true;
        }
    ),
    new Test(
        "Failing test",
        function($p) {
            return "this is a test that always fails";
        }
    )
];

$params = [
    new TestParameter(
        "departments",
        ["Warsaw", "Berlin"],
        "[Warsaw,Berlin]",
        function($str) {
            return explode(',', substr($str, 1, -1));
        },
        function($str) {
            if (preg_match("/^[\\[][a-zA-Z0-9 ,]+[\\]]$/", $str) === 1) {
                return true;
            } else {
                return "Departments parameter must form an array, for example [Department name, Department2 name]\n";
            }
        }
    ),
    new TestParameter(
        "currency",
        "PLN",
        "PLN",
        function($str) {
            return $str;
        },
        function($str) {
            if (empty(trim($str))) {
                return "Currency must not be empty.\n";
            } else {
                return true;
            }
        }
    )
];

Console::main($tests, $params);
