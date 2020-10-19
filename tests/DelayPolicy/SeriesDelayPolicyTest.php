<?php

namespace Fostam\Retry;

use Fostam\Retry\DelayPolicy\SeriesDelayPolicy;
use PHPUnit\Framework\TestCase;

final class SeriesDelayPolicyTest extends TestCase {
    /**
     * @dataProvider TestValueProvider
     * @param array $providedValues
     * @param array $expectedValues
     */
    public function testValues(array $providedValues, array $expectedValues) {
        $policy = new SeriesDelayPolicy($providedValues);
        foreach($expectedValues as $expectedValue) {
            $result = $policy->getDelay();
            $this->assertEquals($expectedValue, $result);
        }
    }

    /**
     * @dataProvider TestValueProvider
     * @param array $providedValues
     * @param array $expectedValues
     */
    public function testKeepLastValue(array $providedValues, array $expectedValues) {
        $policy = new SeriesDelayPolicy($providedValues);
        $lastExpectedValue = -1;
        foreach($expectedValues as $expectedValue) {
            $result = $policy->getDelay();
            $this->assertEquals($expectedValue, $result);
            $lastExpectedValue = $expectedValue;
        }

        for($i=0; $i<10; $i++) {
            $result = $policy->getDelay();
            $this->assertEquals($lastExpectedValue, $result);
        }
    }

    /**
     * @dataProvider TestValueProvider
     * @param array $providedValues
     * @param array $expectedValues
     */
    public function testRepeatValue(array $providedValues, array $expectedValues) {
        $policy = new SeriesDelayPolicy($providedValues, true);

        for($i=0; $i<5; $i++) {
            foreach ($expectedValues as $expectedValue) {
                $result = $policy->getDelay();
                $this->assertEquals($expectedValue, $result);
            }
        }
    }

    public function TestValueProvider() {
        return [
            [ [1, 50, 100, 1000, 10000, 100000], [1, 50, 100, 1000, 10000, 100000] ],
        ];
    }
}