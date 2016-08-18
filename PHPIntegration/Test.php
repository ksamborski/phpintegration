<?php

namespace PHPIntegration;

use PHPIntegration\TestResult;

class Test
{
    private $id;
    private $runable;
    private $tl;

    public function __construct(string $id, callable $run, int $timeLimit = null)
    {
        $this->id = $id;
        $this->runable = $run;
        $this->tl = $timeLimit;
    }

    public function name() : string
    {
        return $this->id;
    }

    public function timeLimit()
    {
        return $this->tl;
    }

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
