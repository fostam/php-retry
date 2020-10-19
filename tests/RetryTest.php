<?php

namespace Fostam\Retry;

use Exception;
use Fostam\Retry\DelayPolicy\NoneDelayPolicy;
use Fostam\Retry\Exception\RetryLimitException;
use PHPUnit\Framework\TestCase;

final class RetryTest extends TestCase {
    private $cnt;

    public function resetCounter(): void {
        $this->cnt = 0;
    }

    public function succeedOnThirdAttempt(): bool {
        $this->cnt++;
        if ($this->cnt === 3) {
            return true;
        }

        return false;
    }

    // onFailure()

    public function testOnFailureSuccess(): void {
        $maxTries = 5;
        $tries = 0;
        Retry::onFailure(function() { return true; }, $maxTries, null, $tries);
        $this->assertEquals(1, $tries);
    }

    public function testOnFailureFailure(): void {
        $maxTries = 5;
        $tries = 0;
        $this->expectException(RetryLimitException::class);
        Retry::onFailure(function() { return false; }, $maxTries, null, $tries);
    }

    // onException()

    public function testOnExceptionSuccess(): void {
        $maxTries = 5;
        $tries = 0;
        Retry::onException(function() { return true; }, $maxTries, null, $tries);
        $this->assertEquals(1, $tries);
    }

    public function testOnExceptionSuccessWithResult(): void {
        $maxTries = 5;
        $tries = 0;
        Retry::onException(function() { return false; }, $maxTries, null, $tries);
        $this->assertEquals(1, $tries);
    }

    public function testOnExceptionFailure(): void {
        $maxTries = 5;
        $this->expectException(RetryLimitException::class);
        Retry::onException(function() { throw new Exception('fail'); }, $maxTries);
    }

    // onCondition()

    public function testOnConditionSuccess(): void {
        $maxTries = 5;
        $tries = 0;
        Retry::execute(function() { return 'test'; }, function($result) {
            return $result === 'test';
        }, $maxTries, null, $tries);
        $this->assertEquals(1, $tries);
    }

    public function testOnConditionSuccessOnThirdAttempt(): void {
        $maxTries = 5;
        $tries = 0;
        Retry::execute([$this, 'succeedOnThirdAttempt'], function($result) {
            return $result === true;
        }, $maxTries, null, $tries);
        $this->assertEquals(3, $tries);
    }

    public function testOnConditionFailure(): void {
        $maxTries = 5;
        $this->expectException(RetryLimitException::class);
        Retry::execute(function() { return 'test'; }, function($result) {
            return $result === 'notest';
        }, $maxTries);
    }

    // execute()

    public function testExecuteSuccess(): void {
        $maxTries = 5;
        $tries = 0;
        Retry::execute(function() { return true; }, null, $maxTries, null, $tries);
        $this->assertEquals(1, $tries);
    }

    public function testExecuteSuccessResult(): void {
        $maxTries = 5;
        $tries = 0;
        $result = Retry::execute(function() { return 'test'; }, null, $maxTries, null, $tries);
        $this->assertEquals(1, $tries);
        $this->assertEquals('test', $result);
    }

    public function testExecuteFailure(): void {
        $maxTries = 5;
        $this->expectException(RetryLimitException::class);
        Retry::execute(function() { return false; }, function($result) {
            return $result !== false;
        }, $maxTries);
    }

    public function testSuccessOnThirdAttempt(): void {
        $maxTries = 5;
        $tries = 0;
        $this->resetCounter();
        Retry::execute([$this, 'succeedOnThirdAttempt'], function($result) {
            return $result !== false;
        }, $maxTries, null, $tries);
        $this->assertEquals(3, $tries);
    }

    public function testFailureBeforeThirdAttempt(): void {
        $maxTries = 2;
        $this->resetCounter();
        $this->expectException(RetryLimitException::class);
        Retry::execute([$this, 'succeedOnThirdAttempt'], function($result) {
            return $result !== false;
        }, $maxTries);
    }

    public function testFailureWithException(): void {
        $maxTries = 5;
        $this->expectException(RetryLimitException::class);
        Retry::execute(function() { throw new Exception('fail'); }, function($result) {
            return $result !== false;
        }, $maxTries);
    }

    public function testFailureWithDelayPolicy(): void {
        $maxTries = 5;
        $this->expectException(RetryLimitException::class);
        Retry::execute(function() { return false; }, function($result) {
            return $result !== false;
        }, $maxTries, new NoneDelayPolicy());
    }
}