<?php

require __DIR__ . '/../vendor/autoload.php';

use PHPIntegration\TestParameter;
use PHPIntegration\Test;
use PHPIntegration\TestGroup;
use PHPIntegration\Console;
use PHPIntegration\Utils\ArrayHelper;
use PHPIntegration\Utils\TreeHelper;
use PHPIntegration\Utils\RandomHelper;

function validate(array $a) : bool
{
    return array_key_exists("required_field_1", $a)
        && array_key_exists("required_field_2", $a["required_field_1"]);
}

$groups = [
    new TestGroup(
        "Path tests",
        [
            new Test(
                "Test1",
                "Check validation function (missing random required field)",
                function ($p) {
                    /*
                     * Let's pretend we have some json that should contain some
                     * required fields. And we have some function that checks it and we
                     * want to test that function.
                     *
                     * First of all we can make use of TreeHelper class.
                     */

                    $requiredFields = [
                        "required_field_1" => [
                            "required_field_2",
                            "required_field_3",
                            "required_field_4" => [
                                "required_field_5"
                            ]
                        ],
                    ];

                    /*
                     * Let's define our json (a valid one). Notice that
                     * required_field_4 has arrays as elements.
                     */

                    $json = [
                        "required_field_1" => [
                            "required_field_2" => "some value",
                            "required_field_3" => "some other value",
                            "required_field_4" => [
                                ["required_field_5" => "third value"],
                                ["other_field" => false]
                            ]
                        ],
                        "other_field_2" => false
                    ];

                    /*
                     * Now, the TreeHelper class can generate a path to a random
                     * element. After that we can unset that element and check if our
                     * validation function sees the change.
                     *
                     * Validation only checks for required_field_2 so to see an error
                     * we should run this file with -n parameter.
                     */
                    $path = TreeHelper::randomPath($requiredFields);
                    ArrayHelper::unsetPath($path, $json);

                    if (validate($json)) {
                        return 'Validate should detect lack of ' . implode(' -> ', $path) . ' field.';
                    }

                    return true;
                }
            ),
            new Test(
                "Test2",
                "Check validation function (missing all required fields)",
                function ($p) {
                    $requiredFields = [
                        "required_field_1" => [
                            "required_field_2",
                            "required_field_3",
                            "required_field_4" => [
                                "required_field_5"
                            ]
                        ],
                    ];

                    $json = [
                        "required_field_1" => [
                            "required_field_2" => "some value",
                            "required_field_3" => "some other value",
                            "required_field_4" => [
                                ["required_field_5" => "third value"],
                                ["other_field" => false]
                            ]
                        ],
                        "other_field_2" => false
                    ];

                    /*
                     * This time we will check for every possible missing field but
                     * only one at a time. You could of course just make a json that
                     * doesn't contain any of the required fields. But then you
                     * wouldn't know if our validation function check only one field
                     * and not the others.
                     *
                     * We may run this test only once because it checks for every
                     * possible combination.
                     */
                    $paths = TreeHelper::allPaths($requiredFields);
                    shuffle($paths);
                    foreach ($paths as $path) {
                        $test = $json; //make sure we unset elements only from copied array
                        ArrayHelper::unsetPath($path, $test);

                        if (validate($test)) {
                            return 'Validate should detect lack of ' . implode(' -> ', $path) . ' field.';
                        }
                    }

                    return true;
                }
            )
        ]
    )
];

$params = function () {
    return [];
};

Console::main($groups, $params);

