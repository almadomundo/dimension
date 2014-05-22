<?php
namespace Universal\Operators\Numeric\Integer\Arithmetic;

class Factory
{
    public function create()
    {
        $op = new Field;
        $op->setSummationOperator(new Summation);
        $op->setMultiplicationOperator(new Multiplication);
        $op->setSubtractionOperator(new Subtraction);
        $op->setDivisionOperator(new Division);
        $op->setNegationOperator(new Negation);
        $op->setReciprocalOperator(new Reciprocal);
        return $op;
    }
}