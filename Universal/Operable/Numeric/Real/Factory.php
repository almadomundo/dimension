<?php
namespace Universal\Operable\Numeric\Real;
use Universal\Operators\Numeric\Real as RealOperators;

class Factory
{
    public function create($value)
    {
        $zero   = $this->createWithValue(0);
        $number = $this->createWithValue($value, $zero);

        return $number;
    }
    
    protected function createWithValue($value, $zero=null)
    {
        $number = new Number();
        if(isset($zero))
        {
            $number->setArithmeticZero($zero);
        }
        
        $number->set($value);
        $number->setEqualityOperator(new RealOperators\Comparison\Equality);
        $number->setInequalityOperator(new RealOperators\Comparison\Inequality);   
        $number->setSummationOperator(new RealOperators\Arithmetic\Summation);
        $number->setSubtractionOperator(new RealOperators\Arithmetic\Subtraction);
        $number->setMultiplicationOperator(new RealOperators\Arithmetic\Multiplication);
        $number->setDivisionOperator(new RealOperators\Arithmetic\Division);
        $number->setNegationOperator(new RealOperators\Arithmetic\Negation);
        $number->setReciprocalOperator(new RealOperators\Arithmetic\Reciprocal);
        
        return $number;
    }
}
