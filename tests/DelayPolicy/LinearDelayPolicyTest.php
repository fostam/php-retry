<?php

namespace Fostam\Retry;

use Fostam\Retry\DelayPolicy\LinearDelayPolicy;
use PHPUnit\Framework\TestCase;

final class LinearDelayPolicyTest extends TestCase {
    /**
     * @dataProvider TestValueProvider
     * @param int $providedValue
     * @param array $expectedValues
     */
    public function testValues(int $providedValue, array $expectedValues) {
        $policy = new LinearDelayPolicy($providedValue);
        foreach($expectedValues as $expectedValue) {
            $result = $policy->getDelay();
            $this->assertEquals($expectedValue, $result);
        }
    }

    public function TestValueProvider() {
        return [
            [ 1, [1, 2, 3, 4, 5, 6] ],
            [ 2, [2, 4, 6, 8, 10, 12] ],
            [ 1000, [1000, 2000, 3000, 4000, 5000, 6000] ],
        ];
    }
}