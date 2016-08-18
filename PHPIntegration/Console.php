<?php

namespace PHPIntegration;

use PHPIntegration\TestParameter;
use PHPIntegration\Console\Printer;
use PHPIntegration\Utils\ArrayHelper;
use PHPIntegration\Utils\FunctionHelper;
use PHPIntegration\Utils\ValueHelper;

class Console
{
    private static function options() : array
    {
        $short  = "";
        $short .= "t:"; //-t "test name"
        $short .= "p:"; //-p "parameter:value"
        $short .= "h";  //-h

        $long = [
            "test:",      //--test "test name"
            "parameter:", //--parameter "parameter:value"
            "help"        //--help
        ];

        return getopt($short, $long);
    }

    private static function parseParameterValue($optvalue) : array
    {
        return explode(':', $optvalue, 2);
    }

    private static function parseOptions(array $options) : array
    {
        $parsedOptions = [
            "tests" => [],
            "help" => false,
            "params" => []
        ];

        foreach ($options as $optname => $optval) {
            switch ($optname) {
            case "p":
            case "parameter":
                if (is_array($optval)) {
                    $parsedOptions["params"] = array_map(
                        Console::parseParameterValue,
                        $optval
                    );
                } else {
                    $parsedOptions["params"] = [Console::parseParameterValue($optval)];
                }
                break;
            case "t":
            case "test":
                if (is_array($optval)) {
                    $parsedOptions["tests"] = $optval;
                } else {
                    $parsedOptions["tests"] = [$optval];
                }
                break;
            case "h":
            case "help":
                $parsedOptions["help"] = true;
                break;
            }
        }

        return $parsedOptions;
    }

    private static function validateOptions(array $parsedOptions, array /*Test*/ $tests, array /*TestParameter*/ $params)
    {
        $paramsMap = ArrayHelper::associative($params, FunctionHelper::callObjMethod('name'));
        $testsMap = ArrayHelper::associative($tests, FunctionHelper::callObjMethod('name'));

        foreach ($parsedOptions["tests"] as $test) {
            if (!array_key_exists($test, $testsMap)) {
                return "Bad test name: " . $test . "\n"
                     . "Available test are: \n"
                     . array_reduce(
                         $tests,
                         function ($r, $t) {
                             return $r . "- " . $t->name() . "\n";
                         },
                         ""
                       );
            }
        }

        foreach ($parsedOptions["params"] as $param) {
            if (count($param) != 2) {
                return "Bad parameter format: " . $param[0] . ", it must be `param_name:param_value`\n";
            } else if (!array_key_exists($param[0], $paramsMap)) {
                return "Bad parameter name: " . $param[0] . "\n"
                     . "Available params are: \n"
                     . array_reduce(
                         $params,
                         function ($r, $p) {
                             return $r . "- " . $p->name() . "\n";
                         },
                         ""
                       );
            }

            $validParam = $paramsMap[$param[0]]->validate($param[1]);
            if ($validParam !== true) {
                return "Bad param `" . $param[0] . "` value `" . $param[1] . "`\n"
                     . $validParam;
            }
        }

        return true;
    }

    private static function help(array /*Test*/ $tests, array /*TestParameter*/ $params)
    {
        global $argv;
        return Printer::yellow("Usage:") . " php " . $argv[0] . " [OPTIONS]\n\n"
             . Printer::green("  -t, --test TEST_NAME ")
             . "\t\t\t\t\t Run only given tests (you can pass multiple -t option) \n"
             . Printer::green("  -p, --parameter PARAMETER_NAME:PARAMETER_VALUE ")
             . "\t Set test parameter (you can pass multiple -p option) \n"
             . Printer::green("  -h, --help ")
             . "\t\t\t\t\t\t Show this help\n\n"
             . Printer::yellow("Available tests:\n")
             . array_reduce(
                   $tests,
                   function ($r, $t) {
                       return $r . "- " . $t->name() . "\n";
                   },
                   ""
               )
             . Printer::yellow("\nAvailable parameters:\n")
             . array_reduce(
                   $params,
                   function ($r, $p) {
                       return $r . "- " . $p->name() . " \n  "
                            . Printer::red("Default: ") . $p->rawDefault() . "\n";
                   },
                   ""
               );
             ;
    }

    private static function buildParameters(array $rawparams, array $paramsMap)
    {
        return array_reduce(
            $rawparams,
            function($res, $p) use ($paramsMap) {
                $res[$p[0]] = $paramsMap[$p[0]]->build($p[1]);
                return $res;
            },
            []
        );
    }

    private static function overrideParameters(
        array $rawparams,
        array /*TestParameter*/ $params
    ) : array {
        $paramsMap = ArrayHelper::associative($params, FunctionHelper::callObjMethod('name'));
        $newParams = Console::buildParameters($rawparams, $paramsMap);

        $defaultParams = [];
        foreach ($paramsMap as $pname => $p) {
            $defaultParams[$p->name()] = $p->default();
        }

        return array_merge($defaultParams, $newParams);
    }

    public static function main(array /*Test*/ $tests, array /*TestParameter*/ $params)
    {
        $parsedOptions = Console::parseOptions(Console::options());
        $validOptions = Console::validateOptions($parsedOptions, $tests, $params);

        if ($validOptions !== true) {
            echo $validOptions;
            exit(1);
        }

        if ($parsedOptions["help"] === true) {
            echo Console::help($tests, $params);
            exit(0);
        }

        $runParams = Console::overrideParameters($parsedOptions["params"], $params);

        $exit = 0;
        $testsMap = ArrayHelper::associative($tests, FunctionHelper::callObjMethod('name'));

        foreach (
            ValueHelper::ifEmpty($parsedOptions['tests'], array_keys($testsMap))
            as $testName) {

            $test = $testsMap[$testName];

            echo $test->name() . " ";
            $result = $test->run($runParams);

            if ($result->isFailed()) {
                $exit = 1;
                echo Printer::red('[ FAILED ] ') . number_format($result->executionTime(), 2) . " ms\n";
                echo $result->failMessage() . "\n";
            } else {
                echo Printer::green('[ OK ] ') . number_format($result->executionTime(), 2) . " ms\n";
            }

            echo "\n";
        }
        
        exit($exit);
    }
}
