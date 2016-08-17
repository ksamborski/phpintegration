<?php

namespace PHPIntegration;

class TestResult
{
    private $status;
    private $message;
    private $time;

    private function __construct($status, $message, $time)
    {
        $this->status = $status;
        $this->message = $message;
        $this->time = $time;
    }
    
    private static function okStatus() : int
    {
        return 0;
    }

    private static function failedStatus() : int
    {
        return 1;
    }

    public static function fail(string $message, float $executionTime) : TestResult
    {
        return new TestResult(TestResult::failedStatus(), $message, $executionTime);
    }
    
    public static function ok(float $executionTime) : TestResult
    {
        return new TestResult(TestResult::okStatus(), "", $executionTime);
    }

    public function isFailed() : bool
    {
        return $this->status == TestResult::failedStatus();
    }

    public function isOk() : bool
    {
        return $this->status == TestResult::okStatus();
    }

    public function failMessage() : string
    {
        return $this->message;
    }

    public function executionTime() : float
    {
        return $this->time;
    }
}
