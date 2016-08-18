<?php

namespace PHPIntegration;

/**
 * Type representing test result.
 */
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

    /**
     * Failed test.
     * @param string $message Some message of what went wrong.
     * @param float $executionTime Time of the test's case execution in ms
     * @return \PHPIntegration\TestResult
     */
    public static function fail(string $message, float $executionTime) : TestResult
    {
        return new TestResult(TestResult::failedStatus(), $message, $executionTime);
    }
    
    /**
     * Succeeded test.
     * @param float $executionTime Time of the test's case execution in ms
     * @return \PHPIntegration\TestResult
     */
    public static function ok(float $executionTime) : TestResult
    {
        return new TestResult(TestResult::okStatus(), "", $executionTime);
    }

    /**
     * Checks if test result is failed.
     * @return bool True when it's failed.
     */
    public function isFailed() : bool
    {
        return $this->status == TestResult::failedStatus();
    }

    /**
     * Checks if test result is succeeded..
     * @return bool True when it's succeeded..
     */
    public function isOk() : bool
    {
        return $this->status == TestResult::okStatus();
    }

    /**
     * Message of what went wrong if the test result is failed one.
     * @return string Message
     */
    public function failMessage() : string
    {
        return $this->message;
    }

    /**
     * Test's case execution time in ms.
     * @return float
     */
    public function executionTime() : float
    {
        return $this->time;
    }
}
