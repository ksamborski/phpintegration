<?php

require __DIR__ . '/../vendor/autoload.php';

use PHPIntegration\TestParameter;
use PHPIntegration\Test;
use PHPIntegration\TestGroup;
use PHPIntegration\Console;

$groups = [
    new TestGroup(
        "Basic tests",
        [
            new Test(
                "Test1",
                "Simple test 1",
                function ($p) {
                    $p["measure"](
                        "1st API call",
                        function () {
                            usleep(rand(10000, 100000));
                            return true;
                        }
                    );

                    return $p["measure"](
                        "2nd API call",
                        function () {
                            usleep(rand(10000, 100000));
                            return true;
                        },
                        55
                    );
                }
            ),
            new Test(
                "Test2",
                "Simple test 2",
                function ($p) {
                    $p["measure"](
                        "1st API call",
                        function () {
                            usleep(rand(10000, 100000));
                            return true;
                        }
                    );

                    return $p["measure"](
                        "2nd API call",
                        function () {
                            usleep(rand(10000, 100000));
                            return true;
                        }
                    );
                }
            )
        ]
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

Console::main($groups, $params);
