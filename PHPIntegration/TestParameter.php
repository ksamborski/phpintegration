<?php

namespace PHPIntegration;

use PHPIntegration\Utils\ArrayHelper;
use PHPIntegration\Console\Printer;

class TestParameter
{
    private $id;
    private $validator;
    private $def;
    private $rawdef;
    private $builder;

    public function __construct(
        string $name,
        $default,
        string $rawDefault,
        callable $builder,
        callable $validator
    ) {
        $this->id = $name;
        $this->validator = $validator;
        $this->def = $default;
        $this->builder = $builder;
        $this->rawdef = $rawDefault;
    }

    public function name() : string
    {
        return $this->id;
    }
    
    public function default()
    {
        return $this->def;
    }

    public function rawDefault()
    {
        return $this->rawdef;
    }

    public function validate(string $value)
    {
        return call_user_func($this->validator, $value);
    }
    
    public function build(string $value)
    {
        return call_user_func($this->builder, $value);
    }

    public static function intParameter(string $name, int $default) : TestParameter
    {
        return new TestParameter(
            $name,
            $default,
            (string) $default,
            function ($val) {
                return (int) $val;
            },
            function ($val) {
                if (preg_match("/^[0-9]+$/", $val) === 1) {
                    return true;
                } else {
                    return "Value `" . $val . "` must be an integer.\n";
                }
            }
        );
    }

    public static function stringParameter(string $name, string $default) : TestParameter
    {
        return new TestParameter(
            $name,
            $default,
            $default,
            function ($val) {
                return $val;
            },
            function ($val) {
                if (empty($val)) {
                    return "Value must not be empty.\n";
                } else {
                    return true;
                }
            }
        );
    }

    public static function regexParameter(string $name, string $default, string $regex) : TestParameter
    {
        return new TestParameter(
            $name,
            $default,
            $default,
            function ($val) {
                return $val;
            },
            function ($val) use ($regex) {
                if (preg_match($regex, $val) === 1) {
                    return true;
                } else {
                    return "Value is not correct.\n";
                }
            }
        );
    }

    public static function oneFromParameter(string $name, $default, array $possibleValues) : TestParameter
    {
        return new TestParameter(
            $name,
            $default,
            (string) $default,
            function ($val) {
                return $val;
            },
            function ($val) use ($possibleValues) {
                if (in_array($val, $possibleValues)) {
                    return true;
                } else {
                    return "Value must be the one of: " . implode(', ', $possibleValues) . ".\n";
                }
            }
        );
    }

    public static function manyFromParameter(string $name, array $default, array $possibleValues) : TestParameter
    {
        return new TestParameter(
            $name,
            $default,
            '[' . implode(',', $default) . ']',
            function ($val) {
                return explode(',', substr($val, 1, -1));
            },
            function ($val) use ($possibleValues) {
                if (preg_match("/^[\\[].+[\\]]$/", $val) === 1) {
                    if (ArrayHelper::every(
                            function($v) use ($possibleValues) {
                                return in_array($v, $possibleValues);
                            },
                            explode(',', substr($val, 1, -1)))) {
                        return true;
                    } else {
                        return "One of the items is not correct. Possible values are:\n"
                            . Printer::listValues($possibleValues);
                    }
                } else {
                    return "Value must form an array, for example [val1, val2].\n";
                }
            }
        );
    }

    public static function arrayOfParameter(string $name, array $default, $testParameter) : TestParameter
    {
        $tp = call_user_func($testParameter, $name, $default[0]);

        return new TestParameter(
            $name,
            $default,
            '[' . implode(',', $default) . ']',
            function ($val) use ($tp) {
                return array_map([$tp, 'build'], explode(',', substr($val, 1, -1)));
            },
            function ($val) use ($tp) {
                if (preg_match("/^[\\[].+[\\]]$/", $val) === 1) {
                    $errors = array_filter(
                        array_map([$tp, 'validate'], explode(',', substr($val, 1, -1))),
                        function($v) {
                            return $v !== true;
                        }
                    );

                    if (empty($errors)) {
                        return true;
                    } else {
                        return implode('', $errors);
                    }
                } else {
                    return "Value must form an array, for example [val1, val2].\n";
                }
            }
        );
    }
}
