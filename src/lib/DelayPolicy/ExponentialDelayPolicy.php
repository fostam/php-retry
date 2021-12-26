<?php

namespace Fostam\Retry\DelayPolicy;

class ExponentialDelayPolicy implements DelayPolicyInterface {
    private int $delayMS;
    private float $multiplier;

    /**
     * ExponentialDelayPolicy constructor.
     *
     * @param int $delayMS
     * @param float $multiplier
     */
    public function __construct(int $delayMS, float $multiplier = 2) {
        $this->delayMS = $delayMS;
        $this->multiplier = $multiplier;
    }

    /**
     *
     */
    public function getDelay(): int {
        $delay = $this->delayMS;
        $this->delayMS = intval(round($this->delayMS * $this->multiplier));
        return $delay;
    }
}