<?php

namespace Fostam\Retry;

use Exception;
use Fostam\Retry\DelayPolicy\DelayPolicyInterface;
use Fostam\Retry\Exception\RetryLimitException;
use Fostam\Retry\Exception\AbortException;

class Retry {
    /**
     * @param callable $payload
     * @param int $count
     * @param DelayPolicyInterface|null $delayPolicy
     * @param int &$tries
     *
     * @return mixed
     * @throws RetryLimitException
     * @throws AbortException
     */
    public static function onFailure(Callable $payload, int $count, ?DelayPolicyInterface $delayPolicy = null, int &$tries = 0) {
        return self::execute($payload, function($result) {
            return $result !== false;
        }, $count, $delayPolicy, $tries);
    }

    /**
     * @param callable $payload
     * @param int $count
     * @param DelayPolicyInterface|null $delayPolicy
     * @param int &$tries
     *
     * @return mixed
     * @throws RetryLimitException
     * @throws AbortException
     */
    public static function onException(Callable $payload, int $count, ?DelayPolicyInterface $delayPolicy = null, int &$tries = 0) {
        return self::execute($payload, function() {
            return true;
        }, $count, $delayPolicy, $tries);
    }

    /**
     * @param callable $payload
     * @param callable $condition
     * @param int $count
     * @param DelayPolicyInterface|null $delayPolicy
     * @param int &$tries
     *
     * @return mixed
     * @throws RetryLimitException
     * @throws AbortException
     */
    public static function onCondition(Callable $payload, Callable $condition, int $count, ?DelayPolicyInterface $delayPolicy = null, int &$tries = 0) {
        return self::execute($payload, $condition, $count, $delayPolicy, $tries);
    }

    /**
     * @param callable $payload
     * @param callable|null $resultTester
     * @param int $count
     * @param DelayPolicyInterface|null $delayPolicy
     * @param int &$tries
     *
     * @return mixed
     * @throws RetryLimitException
     * @throws AbortException
     */
    public static function execute(Callable $payload, ?Callable $resultTester, int $count, ?DelayPolicyInterface $delayPolicy = null, int &$tries = 0) {
        $tries = 1;

        do {
            $e = null;
            $success = true;
            $callableResult = null;

            try {
                $callableResult = call_user_func($payload);
                if ($resultTester) {
                    $success = call_user_func($resultTester, $callableResult);
                }
            } catch (AbortException $e) {
                // throw on
                throw $e;
            } catch (Exception $e) {
                $success = false;
            }

            if ($success) {
                return $callableResult;
            }

            if ($tries >= $count) {
                throw new RetryLimitException($tries, $e);
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