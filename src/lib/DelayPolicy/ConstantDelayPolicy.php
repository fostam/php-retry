<?php

namespace Fostam\Retry\DelayPolicy;

class ConstantDelayPolicy implements DelayPolicyInterface {
    private $delayMS;

    /**
     * ConstantDelayHandler constructor.
     *
     * @param int $delayMS
     */
    public function __construct(int $delayMS) {
        $this->delayMS = $delayMS;
    }

    /**
     *
     */
    public function getDelay(): int {
        return $this->delayMS;
    }
}