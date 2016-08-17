<?php

namespace PHPIntegration;

use PHPIntegration\TestResult;

class Test
{
    private $id;
    private $runable;

    public function __construct(string $id, callable $run)
    {
        $this->id = $id;
        $this->runable = $run;
    }

    public function name() : string
    {
        return $this->id;
    }

    public function run(array $params) : TestResult
    {
        $t1 = microtime(true);
        $failed = false;
        $msg = '';

        try {
            $res = call_user_func($this->runable, [$params]);
            $failed = $res !== true;
            if ($failed) {
                $msg = $res;
            }
        } catch (Exception $e) {
            $msg = $e->getMessage();
            $failed = true;
        }

        $t2 = microtime(true);

        if ($failed) {
            return TestResult::fail($msg, $t2 - $t1);
        } else {
            return TestResult::ok($t2 - $t1);
        }
    }
}
