<?php

namespace PHPIntegration;

use PHPIntegration\Utils\ArrayHelper;
use PHPIntegration\Console\Printer;
use PHPIntegration\Testable;

/**
 * Type representing dynamic parameter. It can have default value that can be
 * override by user via shell arguments. Parameters are passed to every test
 * case.
 */
class TestParameter
{
    private $id;
    private $validator;
    private $def;
    private $rawdef;
    private $builder;

    /**
     * Constructs parameter.
     *
     * @param string $name Name of the parameter.
     * @param mixed $default Default value of the parameter. It will be used
     *                        when user won't provide it via shell argument.
     * @param string $rawDefault User can pass only string via shell so this is
     *                           string representation of the default value provided above.
     *                           It's only used for displaying the value for the user.
     * @param callable $builder Function that should take string representation of the value and
     *                          return value of the same type as $default is.
     * @param callable $validator Function that should validate value provided
     *                            by user. It should return true if it's valid
     *                            and string with message if something is wrong.
     */
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

    /**
     * Name of the parameter.
     * @return string Parameter's name
     */
    public function name() : string
    {
        return $this->id;
    }
    
    /**
     * Default value of the parameter.
     * @return string Default value
     */
    public function default()
    {
        return $this->def;
    }

    /**
     * String representation of the default value.
     * @return string Default value as string.
     */
    public function rawDefault() : string
    {
        return $this->rawdef;
    }

    /**
     * Validates raw string value provided by user.
     * It executes the validator function given in the constructor.
     * @param string $value Value provided by user via shell.
     * @return string|bool Returns true if everything's fine and string message
     *                     when something's wrong.
     */
    public function validate(string $value)
    {
        return call_user_func($this->validator, $value);
    }
    
    /**
     * Builds value from string provided by user via shell.
     * It executes the builder function given in the constructor.
     * @param string $value Value provided by user via shell.
     * @return mixed Value of the same type as $default
     */
    public function build(string $value)
    {
        return call_user_func($this->builder, $value);
    }

    /**
     * Creates parameter that is an integer.
     * @param string $name Name of the parameter.
     * @param int $default Default value of the parameter.
     * @return \PHPIntegration\TestParameter
     */
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

    /**
     * Creates parameter that is a string. It checks if it's not empty.
     * @param string $name Name of the parameter.
     * @param string $default Default value of the parameter.
     * @return \PHPIntegration\TestParameter
     */
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

    /**
     * Creates parameter that is a string. It checks if it satisfies provided
     * regular expression.
     * @param string $name Name of the parameter.
     * @param string $default Default value of the parameter.
     * @param string $regex Regular expression to check in the validate method
     * @return \PHPIntegration\TestParameter
     */
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

    /**
     * Creates parameter that is a value from list of defined values.
     * It checks if it belongs to provided array of values.
     * @param string $name Name of the parameter.
     * @param string $default Default value of the parameter.
     * @param array $possibleValues Array of possible values.
     * @return \PHPIntegration\TestParameter
     */
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

    /**
     * Creates parameter that is an array of values from the list of defined values.
     * It checks if every value belongs to provided array of values.
     * @param string $name Name of the parameter.
     * @param array $default Default value of the parameter.
     * @param array $possibleValues Array of possible values.
     * @return \PHPIntegration\TestParameter
     */
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

    /**
     * Creates parameter that is an array of values that are validated by other
     * instance of \PHPIntegration\TestParameter.
     * This way you can combine \PHPIntegration\TestParameter instances, for
     * example arrayOfParameter with regexParameter.
     * @example examples/basic_example.php
     * @param string $name Name of the parameter.
     * @param array $default Default value of the parameter.
     * @param mixed $testParameter Function that takes parameter's name and
     *                             default and returns \PHPIntegration\TestParameter
     * @return \PHPIntegration\TestParameter
     */
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

    /**
     * Function creates object parameter. It must be an instance of
     * \PHPIntegration\Testable.
     * @param string $name Name of the parameter.
     * @param \PHPIntegration\Testable Object that will be taken as default.
     * @param bool $valid Whether to test for valid or invalid object.
     * @return \PHPIntegration\TestParameter
     */
    public static function objectParameter(string $name, Testable $default, bool $valid = true)
    {
        return new TestParameter(
            $name,
            $default,
            $default->asStringParameter(),
            function($val) use ($default) { return $default->build($val); },
            function ($val) use ($default, $valid) {
                return $default->validate($val, $valid);
            }
        );
    }
}
