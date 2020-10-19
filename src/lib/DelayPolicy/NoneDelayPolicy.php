<?php

namespace Fostam\Retry\DelayPolicy;

class NoneDelayPolicy implements DelayPolicyInterface {
    /**
     *
     */
    public function getDelay(): int {
        return 0;
    }
}