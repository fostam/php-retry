<?php

namespace Fostam\Retry;

use Fostam\Retry\DelayPolicy\JitterDelayPolicy;
use PHPUnit\Framework\TestCase;

final class JitterDelayPolicyTest extends TestCase {
    /**
     * @dataProvider TestValueProviderForRealNumbers
     * @param int $providedValue
     * @param int $providedJitter
     * @param array $expectedValueRange
     */
    public function testValuesWithRealRandomNumbers(int $providedValue, int $providedJitter, array $expectedValueRange) {
        $policy = new JitterDelayPolicy($providedValue, $providedJitter);
        for($i=0; $i<50; $i++) {
            $result = $policy->getDelay();
            $this->assertGreaterThanOrEqual($expectedValueRange[0], $result);
            $this->assertLessThanOrEqual($expectedValueRange[1], $result);
        }
    }

    public function TestValueProviderForRealNumbers() {
        return [
            [ 1000, 10, [990, 1010] ],
            [ 1000, 100, [900, 1100] ],
            [ 1000, 0, [1000, 1000] ],
        ];
    }

    /**
     * @dataProvider TestValueProviderForMockedNumbers
     * @param int $providedValue
     * @param int $providedJitter
     * @param array $expectedValues
     */
    public function testValuesWithMockedNumbers(int $providedValue, int $providedJitter, array $expectedValues) {
        mt_srand(0);
        $policy = new JitterDelayPolicy($providedValue, $providedJitter);
        foreach($expectedValues as $expectedValue) {
            $result = $policy->getDelay();
            $this->assertEquals($expectedValue, $result);
        }
    }

    public function TestValueProviderForMockedNumbers() {
        return [
            [ 1000, 10, [1001, 993, 1004, 999, 991, 999, 1003] ],
            [ 1000, 100, [1100, 1038, 1070, 993, 1018, 900, 1066] ],
            [ 1000, 0, [1000, 1000, 1000, 1000, 1000, 1000] ],
        ];
    }
}