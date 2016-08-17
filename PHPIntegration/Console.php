<?php

namespace PHPIntegration;

use PHPIntegration\TestParameter;

class Console
{
    private static function options() : array
    {
        $short = "";
        $short .= "t:"; //-t "test name"
        $short .= "p:"; //-p "parameter:value"
        $short .= "h";  //-h

        $long = [
            "test:",     //--test "test name"
            "parameter:" //--parameter "parameter:value"
            "help"       //--help
        ];

        return getopt($short, $long);
    }

    private static parseParameterValue($optvalue) : array
    {
        return explode(':', $optvalue, 1);
    }

    private static function validateOptions(array $options, array /*TestParameter*/ $params)
    {
        $paramsMap = array_reduce(
            $params,
            function ($arr, $param) {
                $arr[$param->name()] = $param;
            },
            []
        );

        foreach ($options as $optname => $optval) {
            switch ($optname) {
            case "p":
            case "parameter":
                if (is_array($optval)) {
                    array_map(
                        function($val) {
                            $kv = Console::parseParameterValue($optval);
                            
                        },
                        $optval
                    );
                } else {
                    $kv = Console::parseParameterValue($optval);
                    if (array_key_exists($kv[0], $paramsMap)) {
                        $paramsMap[$kv[0]]->validate($kv[1]);
                    }
                }
                break;
            case "t":
            case "test":
                break;
            case "h":
            case "help":
                break;
            }
        }
    }

    public static function main(array /*Test*/ $tests, array /*TestParameter*/ $params)
    {
        $exit = 0;

        Console::validateOptions(Console::options(), $params);

        foreach ($tests as $test) {
            echo "\n" . $test->name() . " ";
            $result = $test->run();

            if ($result->isFailed()) {
                $exit = 1;
                echo '[ FAILED ] ' . number_format($result->executionTime(), 2) . " ms\n";
                echo $result->failMessage() . "\n";
            } else {
                echo '[ OK ] ' . number_format($result->executionTime(), 2) . " ms\n";
            }
        }
        
        exit($exit);
    }
}
