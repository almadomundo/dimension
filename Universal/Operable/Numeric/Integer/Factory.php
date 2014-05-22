<?php
namespace Universal\Operable\Numeric\Integer;
use Universal\Operators\Numeric\Integer as IntegerOperators;

class Factory
{
    public function create($value)
    {
        $number = new Number();
        $number->set($value);
        
        $number->setEqualityOperator(new IntegerOperators\Comparison\Equality);
        $number->setInequalityOperator(new IntegerOperators\Comparison\Inequality);   
        
        $number->setSummationOperator(new IntegerOperators\Arithmetic\Summation);
        $number->setSubtractionOperator(new IntegerOperators\Arithmetic\Subtraction);
        $number->setMultiplicationOperator(new IntegerOperators\Arithmetic\Multiplication);
        $number->setDivisionOperator(new IntegerOperators\Arithmetic\Division);
        $number->setNegationOperator(new IntegerOperators\Arithmetic\Negation);
        $number->setReciprocalOperator(new IntegerOperators\Arithmetic\Reciprocal);
        
        return $number;
    }
}
