<?php
namespace Universal\Operators\Numeric\Integer\Comparison;

class Factory
{
    public function create()
    {
        $op = new Linear;
        $op->setEqualityOperator(new Equality);
        $op->setInequalityOperator(new Inequality);
        return $op;
    }
}