<?php

require __DIR__ . '/../vendor/autoload.php';

use PHPIntegration\TestParameter;
use PHPIntegration\Test;
use PHPIntegration\TestGroup;
use PHPIntegration\Console;

$groups = [
    new TestGroup(
        "Basic tests with pre and post",
        [
            new Test(
                "Test1",
                "Simple test 1",
                function ($p) {
                    usleep(rand(10000, 100000));
                    return true;
                }
            )
        ],
        [
            "pre" => function () { return (rand() % 2) == 1 ? true : "Random failing pre"; },
            "post" => function () { return (rand() % 2) == 1 ? true : "Random failing post"; },
        ]
    ),
    new TestGroup(
        "Basic tests with pre and post 2",
        [
            new Test(
                "Test1",
                "Simple test 1",
                function ($p) {
                    usleep(rand(10000, 100000));
                    return true;
                }
            )
        ],
        [
            "pre" => function () { return (rand() % 2) == 1 ? true : "Random failing pre"; },
            "post" => function () { return (rand() % 2) == 1 ? true : "Random failing post"; },
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
