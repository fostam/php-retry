<?php

namespace Fostam\Retry;

use Exception;
use Fostam\Retry\DelayPolicy\DelayPolicyInterface;
use Fostam\Retry\Exception\RetryLimitException;

class Retry {
    /**
     * @param callable $subject
     * @param int $count
     * @param DelayPolicyInterface|null $delayPolicy
     * @param int &$tries
     *
     * @return int
     * @throws RetryLimitException
     */
    public static function onFailure(Callable $subject, int $count, ?DelayPolicyInterface $delayPolicy = null, int &$tries = 0) {
        return self::execute($subject, function($result) {
            return $result !== false;
        }, $count, $delayPolicy, $tries);
    }

    /**
     * @param callable $subject
     * @param int $count
     * @param DelayPolicyInterface|null $delayPolicy
     * @param int &$tries
     *
     * @return int
     * @throws RetryLimitException
     */
    public static function onException(Callable $subject, int $count, ?DelayPolicyInterface $delayPolicy = null, &$tries = 0) {
        return self::execute($subject, function($result) {
            return true;
        }, $count, $delayPolicy, $tries);
    }

    /**
     * @param callable $subject
     * @param callable $condition
     * @param int $count
     * @param DelayPolicyInterface|null $delayPolicy
     * @param int &$tries
     *
     * @return int
     * @throws RetryLimitException
     */
    public static function onCondition(Callable $subject, Callable $condition, int $count, ?DelayPolicyInterface $delayPolicy = null, &$tries = 0) {
        return self::execute($subject, $condition, $count, $delayPolicy, $tries);
    }

    /**
     * @param callable $subject
     * @param callable|null $resultTester
     * @param int $count
     * @param DelayPolicyInterface|null $delayPolicy
     * @param int &$tries
     *
     * @return mixed
     * @throws RetryLimitException
     */
    public static function execute(Callable $subject, ?Callable $resultTester, int $count, ?DelayPolicyInterface $delayPolicy = null, &$tries = 0) {
        $tries = 1;

        do {
            $e = null;
            $success = true;
            $callableResult = null;

            try {
                $callableResult = call_user_func($subject);
                if ($resultTester) {
                    $success = call_user_func($resultTester, $callableResult);
                }
            } catch (Exception $e) {
                $success = false;
            }

            if ($success) {
                return $callableResult;
            }

            if ($tries >= $count) {
                throw new RetryLimitException("execution failed {$tries} times", 0, $e);
            }

            if ($delayPolicy) {
                $delay = $delayPolicy->getDelay();
                usleep($delay * 1000);
            }

            $tries++;
        }
        while(true);
    }
}