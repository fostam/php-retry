<?php

namespace Fostam\Retry\DelayPolicy;

class JitterDelayPolicy implements DelayPolicyInterface {
    private int $delayMS;
    private int $jitterMS;

    /**
     * JitterDelayPolicy constructor.
     *
     * @param int $delayMS
     * @param int $jitterMS
     */
    public function __construct(int $delayMS, int $jitterMS) {
        $this->delayMS = $delayMS;
        $this->jitterMS = $jitterMS;
    }

    /**
     *
     */
    public function getDelay(): int {
        $jitterUS = mt_rand(-($this->jitterMS), $this->jitterMS);
        return $this->delayMS + $jitterUS;
    }
}