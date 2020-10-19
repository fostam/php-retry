<?php

namespace Fostam\Retry;

use Fostam\Retry\DelayPolicy\ExponentialDelayPolicy;
use PHPUnit\Framework\TestCase;

final class ExponentialDelayPolicyTest extends TestCase {
    /**
     * @dataProvider TestValueProvider
     * @param int $providedValue
     * @param array $expectedValues
     */
    public function testValues(int $providedValue, array $expectedValues) {
        $policy = new ExponentialDelayPolicy($providedValue);
        foreach($expectedValues as $expectedValue) {
            $result = $policy->getDelay();
            $this->assertEquals($expectedValue, $result);
        }
    }

    public function TestValueProvider() {
        return [
            [ 1, [1, 2, 4, 8, 16, 32] ],
            [ 2, [2, 4, 8, 16, 32, 64] ],
            [ 1000, [1000, 2000, 4000, 8000, 16000, 32000] ],
            [ 2000, [2000, 4000, 8000, 16000, 32000, 64000] ],
        ];
    }

    /**
     * @dataProvider TestValueWithMultiplierProvider
     * @param int $providedValue
     * @param float $multiplier
     * @param array $expectedValues
     */
    public function testValuesWithMultiplier(int $providedValue, float $multiplier, array $expectedValues) {
        $policy = new ExponentialDelayPolicy($providedValue, $multiplier);
        foreach($expectedValues as $expectedValue) {
            $result = $policy->getDelay();
            $this->assertEquals($expectedValue, $result);
        }
    }

    public function TestValueWithMultiplierProvider() {
        return [
            [ 1, 2, [1, 2, 4, 8, 16, 32] ],
            [ 2, 2, [2, 4, 8, 16, 32, 64] ],
            [ 1000, 1.5, [1000, 1500, 2250, 3375] ],
            [ 2000, 1.5, [2000, 3000, 4500, 6750] ],
            [ 1, 3, [1, 3, 9, 27, 81, 243] ],
            [ 2, 3, [2, 6, 18, 54, 162, 486] ],
        ];
    }
}