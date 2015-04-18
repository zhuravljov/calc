<?php

use zhuravljov\calc\Calculator;
use zhuravljov\calc\CalculatorException;

class CalculatorTest extends PHPUnit_Framework_TestCase
{
    public function testCalc()
    {
        $this->assertCalcEquals('4', 4);
        $this->assertCalcEquals('-4', -4);

        $this->assertCalcEquals('2 + 2', 4);
        $this->assertCalcEquals('2 - 2', 0);
        $this->assertCalcEquals('2 * 2', 4);
        $this->assertCalcEquals('2 / 2', 1);

        $this->assertCalcEquals('2 + 2 * 2', 6);
        $this->assertCalcEquals('(2 + 2) * 2', 8);

        $this->assertCalcException('(2 + 2 * 2');
        $this->assertCalcException('2 + 2)');
    }

    private function assertCalcEquals($expression, $result)
    {
        $calculator = new Calculator();
        $this->assertEquals($calculator->calc($expression), $result);
    }

    private function assertCalcException($expression)
    {
        $calculator = new Calculator();
        try {
            $calculator->calc($expression);
        } catch (CalculatorException $e) {
            return;
        }
        $this->fail('An expected exception has not been raised.');
    }
}
