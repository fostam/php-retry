<?php

namespace Fostam\Retry;

use Fostam\Retry\DelayPolicy\ConstantDelayPolicy;
use PHPUnit\Framework\TestCase;

final class ConstantDelayPolicyTest extends TestCase {
    /**
     * @dataProvider TestValueProvider
     * @param int $value
     */
    public function testValues(int $value) {
        $policy = new ConstantDelayPolicy($value);
        for($i=0; $i<100; $i++) {
            $result = $policy->getDelay();
            $this->assertEquals($value, $result);
        }
    }

    public function TestValueProvider() {
        return [
            [1], [50], [100], [1000], [10000], [100000], [1000000], [10000000],
        ];
    }
}