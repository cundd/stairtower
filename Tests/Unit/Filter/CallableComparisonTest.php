<?php
declare(strict_types=1);

namespace Cundd\Stairtower\Tests\Unit\Filter;

use Cundd\Stairtower\Tests\Unit\AbstractCase;
use Cundd\Stairtower\Filter\Comparison\CallableComparison;


/**
 * Tests for different comparisons
 */
class CallableComparisonTest extends AbstractCase
{
    /**
     * @test
     */
    public function returnFalseCallbackTest()
    {
        $fixture = new CallableComparison(
            function () {
                return false;
            }
        );
        $this->assertFalse($fixture->perform(null));
    }

    /**
     * @test
     */
    public function returnTrueCallbackTest()
    {
        $fixture = new CallableComparison(
            function () {
                return true;
            }
        );
        $this->assertTrue($fixture->perform(null));
    }
}
 