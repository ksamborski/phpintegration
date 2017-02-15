<?php

namespace PHPIntegration;

/**
 * Class representing a group of tests with optional hooks.
 */
class TestGroup
{
    private $name;
    private $tests;
    private $options;
    
    /**
     * A test group constructor.
     * @param string $name Name of the group
     * @param array $tests An array of \PHPIntegration\Test that belong to this group
     * @param array $options Possible options:
     *                          - "pre": function that will be executed before
     *                            once before running any test from this group.
     *                            If it returns something other than true it
     *                            will stop the tests and display the returning
     *                            value. This function should take on argument
     *                            which is an array with run parameters.
     *                          - "post": function thath will be executed after
     *                            running all tests from this group.
     *                            If it returns something other than true it
     *                            will stop the tests and display the returning
     *                            value. This function should take on argument
     *                            which is an array with run parameters.
     */
    public function __construct(string $name, array /* Test */ $tests, array $options = [])
    {
        $this->name = $name;
        $this->tests = $tests;
        $this->options = $options;
    }

    /**
     * Returns tests belonging to this group.
     * @return array An array of \PHPIntegration\Test
     */
    public function tests() : array
    {
        return array_filter(
            $this->tests,
            function ($t) {
                return $t instanceof \PHPIntegration\Test;
            }
        );
    }

    /**
     * Returns group's name.
     * @return string
     */
    public function name() : string
    {
        return $this->name;
    }

    /**
     * Executes optional "pre" function if it was provided.
     * @return mixed It should return true if succeeded or string with message
     *               otherwise.
     */
    public function pre(array $params)
    {
        if (array_key_exists('pre', $this->options) && is_callable($this->options['pre'])) {
            return call_user_func($this->options['pre'], $params);
        }

        return true;
    }

    /**
     * Executes optional "post" function if it was provided.
     * @return mixed It should return true if succeeded or string with message
     *               otherwise.
     */
    public function post(array $params)
    {
        if (array_key_exists('post', $this->options) && is_callable($this->options['post'])) {
            return call_user_func($this->options['post'], $params);
        }

        return true;
    }
}
