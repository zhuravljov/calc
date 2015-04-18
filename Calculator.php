<?php

namespace zhuravljov\calc;

/**
 * Math expressions calculator
 *
 * @author Roman Zhuravlev <zhuravljov@gmnail.com>
 */
class Calculator
{
    /**
     * @return array supported operations and their priorities
     */
    protected function getOperations()
    {
        return [
            '+' => 1,
            '-' => 1,
            '*' => 2,
            '/' => 2,
        ];
    }

    /**
     * @param string $operation
     * @param float $number1
     * @param float $number2
     * @return float
     * @throws \Exception
     */
    protected function calcOperation($operation, $number1, $number2)
    {
        switch ($operation) {
            case '+':
                return $number1 + $number2;
            case '-':
                return $number1 - $number2;
            case '*':
                return $number1 * $number2;
            case '/':
                return $number1 / $number2;
            default:
                throw new CalculatorException(sprintf('Unknown operation: "%s"', $operation));
        }
    }

    /**
     * Calculate a result of a math expression
     *
     * @param string $expression
     * @return null
     * @throws \Exception
     */
    public function calc($expression)
    {
        if ($tokens = $this->getPoliz($expression)) {
            $stack = [];
            foreach ($tokens as $token) {
                if (is_float($token)) {
                    $stack[] = $token;
                } else {
                    $num2 = array_pop($stack);
                    $num1 = array_pop($stack);
                    if ($num1 === null or $num2 === null) {
                        // Нехватает операндов
                        throw new CalculatorException('Syntax error');
                    }
                    $stack[] = $this->calcOperation($token, $num1, $num2);
                }
            }
            if (count($stack) == 1) {
                return $stack[0];
            } else {
                throw new CalculatorException('Syntax error');
            }
        } else {
            return null;
        }
    }

    /**
     * Returns an expression in the form of RPN
     *
     * @param  string $expression
     * @return array
     * @throws \Exception
     */
    private function getPoliz($expression)
    {
        $operations = $this->getOperations();
        $result = [];
        $stack = [];
        foreach ($this->getTokens($expression) as $token) {
            if (is_float($token)) {
                $result[] = $token;
            } elseif ($token == '(') {
                $stack[] = '(';
            } elseif ($token == ')') {
                do {
                    $top = array_pop($stack);
                    if ($top === null) {
                        throw new CalculatorException('Missing opening quote');
                    }
                    $next = $top !== '(';
                    if ($next) $result[] = $top;
                } while ($next);
            } elseif (isset($operations[$token])) {
                $top = array_pop($stack);
                if ($top !== null && isset($operations[$top]) && $operations[$token] <= $operations[$top]) {
                    $result[] = $top;
                } else {
                    $stack[] = $top;
                }
                $stack[] = $token;
            } else {
                throw new CalculatorException(sprintf('Unknown operation', $token));
            }
        }
        while (($top = array_pop($stack)) !== null) {
            if ($top == '(') throw new CalculatorException('Missing closing quote');
            $result[] = $top;
        }

        return $result;
    }

    /**
     * Return an array of tokens with with the processing of the unary "-"
     *
     * @param  string $expression
     * @return array
     */
    private function getTokens($expression)
    {
        $result = [];
        $last = '(';
        while (($token = $this->getToken($expression, $pos, $len)) !== false) {
            if ($token === '-' && $last === '(') $result[] = 0.;
            $result[] = $last = $token;
        }

        return $result;
    }

    /**
     * Return of a next token
     *
     * @param string $expression
     * @param null|int $pos
     * @param null|int $len
     * @return string|float
     * @throws \Exception
     */
    private function getToken(&$expression, &$pos, &$len)
    {
        $operations = $this->getOperations();
        if ($pos === null) {
            $pos = 0;
            $len = strlen($expression);
        }
        while ($pos < $len) {
            $char = $expression{$pos};
            if ($char == '(' || $char == ')' || isset($operations[$char])) {
                $pos++;

                return $char;
            } elseif (($char >= '0' && $char <= '9') || $char === '.') {
                $number = '';
                $scale = 0;
                do {
                    $number .= $char;
                    if ($char === '.') $scale++;
                    $pos++;
                    $next = $pos < $len;
                    if ($next) {
                        $char = $expression{$pos};
                        $next = ($char >= '0' && $char <= '9') || $char === '.';
                    }
                } while ($next);
                if ($scale <= 1) {
                    return (float)$number;
                } else {
                    throw new CalculatorException(sprintf('Number error: %s', $number));
                }
            } elseif ($char == ' ') {
                $pos++;
            } else {
                throw new CalculatorException(sprintf('Unknown char: %s', $char));
            }
        }
        $pos = null;

        return false;
    }
}

class CalculatorException extends \Exception
{
}
