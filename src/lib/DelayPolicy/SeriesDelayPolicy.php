<?php

namespace Fostam\Retry\DelayPolicy;

class SeriesDelayPolicy implements DelayPolicyInterface {
    private array $delaysMS;
    private int $ptr;
    private bool $repeat;

    /**
     * ConstantDelayHandler constructor.
     *
     * @param array $delaysMS
     * @param bool $repeat
     */
    public function __construct(array $delaysMS, bool $repeat = false) {
        $this->delaysMS = $delaysMS;
        $this->repeat = $repeat;
        $this->ptr = 0;
    }

    /**
     *
     */
    public function getDelay(): int {
        $delayMS = $this->delaysMS[$this->ptr];
        if ($this->ptr === count($this->delaysMS) - 1) {
            if ($this->repeat) {
                $this->ptr = 0;
            }
        }
        else {
            $this->ptr++;
        }

        return $delayMS;
    }
}