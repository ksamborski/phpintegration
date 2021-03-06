<?php

require __DIR__ . '/../vendor/autoload.php';

use PHPIntegration\TestParameter;
use PHPIntegration\Test;
use PHPIntegration\TestGroup;
use PHPIntegration\Console;
use PHPIntegration\Utils\ArrayHelper;
use PHPIntegration\Utils\RandomHelper;

$groups = [
    new TestGroup(
        "Random tests",
        [
            new Test(
                "Test1",
                "Warsaw test",
                function ($p) {
                    if (!in_array('Warsaw', $p['departments'])) {
                        return "this test succeeds only if Warsaw is passed";
                    } else {
                        return true;
                    }
                }
            ),
            new Test(
                "Test2",
                "Failing test",
                function ($p) {
                    usleep(20000);
                    return "this is a test that always fails";
                },
                10
            )
        ]
    )
];

$params = function () {
    return [
        TestParameter::manyFromParameter(
            "departments",
            RandomHelper::randomArray(["Warsaw", "Berlin", "Cracow"], false),
            ["Warsaw", "Berlin", "Cracow"]
        ),
        TestParameter::stringParameter("currency", RandomHelper::randomString(3)),
        TestParameter::arrayOfParameter(
            "hours",
            RandomHelper::randomMany(function() { return rand(1,24); },1),
            '\PHPIntegration\TestParameter::intParameter'
        )
    ];
};

Console::main($groups, $params);
