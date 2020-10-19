<?php

namespace Fostam\Retry\DelayPolicy;

class LinearDelayPolicy implements DelayPolicyInterface {
    private $delayMS;
    private $incrementUS;

    /**
     * LinearDelayHandler constructor.
     *
     * @param int $delayMS
     */
    public function __construct(int $delayMS) {
        $this->incrementUS = $delayMS;
        $this->delayMS = 0;
    }

    /**
     *
     */
    public function getDelay(): int {
        $this->delayMS += $this->incrementUS;
        return $this->delayMS;
    }
}