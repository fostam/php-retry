<?php

namespace Fostam\Retry\DelayPolicy;

interface DelayPolicyInterface {
    /**
     * get next delay in milliseconds
     *
     * @return int
     */
    public function getDelay(): int;
}