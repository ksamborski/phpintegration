<?php

namespace PHPIntegration;

use PHPIntegration\TestResult;

/**
 * Class representing single test case.
 */
class Test
{
    private $id;
    private $desc;
    private $runable;
    private $tl;

    /**
     * Test case constructor.
     * @param string $id Name of the test
     * @param string $desc Test's description.
     * @param callable $run Function running the test,
     *                      it must take one parameter
     *                      that is array of
     *                      parameter name => parameter value
     * @param int $timeLimit Optional. Execution time limit for test,
     *                       useful for benchmarking.
     */
    public function __construct(string $id, string $desc, callable $run, int $timeLimit = null)
    {
        $this->id = $id;
        $this->runable = $run;
        $this->tl = $timeLimit;
        $this->desc = $desc;
    }

    /**
     * Returns test name.
     * @return string Name of the test.
     */
    public function name() : string
    {
        return $this->id;
    }

    /**
     * Returns test description.
     * @return string Test's description..
     */
    public function description() : string
    {
        return $this->desc;
    }

    /**
     * Returns test's time limit.
     * @return int Test's time limit.
     */
    public function timeLimit()
    {
        return $this->tl;
    }

    /**
     * Simply runs the test.
     * @param array $params Array of parameter name => parameter value
     * @return \PHPIntegration\TestResult
     */
    public function run(array $params) : TestResult
    {
        $t1 = microtime(true);
        $failed = false;
        $msg = '';

        try {
            $res = call_user_func($this->runable, $params);
            $failed = $res !== true;
            if ($failed) {
                $msg = $res;
            }
        } catch (Exception $e) {
            $msg = $e->getMessage();
            $failed = true;
        }

        $t2 = microtime(true);
        $duration = ($t2 - $t1) * 1000;

        if ($failed || ($this->tl !== null && $this->tl < $duration)) {
            return TestResult::fail($msg, $duration);
        } else {
            return TestResult::ok($duration);
        }
    }
}
