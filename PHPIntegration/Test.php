<?php

namespace PHPIntegration;

use PHPIntegration\TestResult;

class Test
{
    private $id;
    private $runable;

    public function __construct(string $id, collable $run)
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
            $this->runable($params);
        } catch (Exception $e) {
            $msg = $e->getMessage();
            $failed = true;
        }

        $t2 = microtime(true);

        if ($failed) {
            return TestResult::failed($msg, $t2 - $t1);
        } else {
            return TestResult::ok($t2 - $t1);
        }
    }
}
