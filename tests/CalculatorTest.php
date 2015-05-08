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

        $this->assertCalcException('(2 + 2 * 2', CalculatorException::SYNTAX_ERROR);
        $this->assertCalcException('2 + 2)', CalculatorException::SYNTAX_ERROR);

        $this->assertCalcException('2 ^ 2', CalculatorException::UNKNOWN_OPERATION);
        $this->assertCalcException('1 / 0', CalculatorException::DIV_BY_ZERO);
    }

    private function assertCalcEquals($expression, $result)
    {
        $calculator = new Calculator();
        $this->assertEquals($calculator->calc($expression), $result);
    }

    private function assertCalcException($expression, $code)
    {
        $calculator = new Calculator();
        try {
            $calculator->calc($expression);
        } catch (CalculatorException $e) {
            if ($e->getCode() === $code) {
                return;
            } else {
                $this->fail('Exception code must be ' . $code . ', not ' . $e->getCode() . '.');
            }
        }
        $this->fail('An expected exception has not been raised.');
    }
}
