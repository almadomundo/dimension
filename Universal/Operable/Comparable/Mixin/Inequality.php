<?php
namespace Universal\Operable\Comparable\Mixin;
use Universal\Operators\Comparison as Comparison;
use Universal\Operable as Operable;

trait Inequality
{
    private $inequalityOperator;
    
    public function setInequalityOperator(Comparison\Inequality $operator)
    {
        $this->inequalityOperator = $operator;
    }
    
    public function greaterThan(Operable\Operable $x)
    {
        return $this->inequalityOperator->greater($this->get(), $x->get());
    }
    
    public function lessThan(Operable\Operable $x)
    {
        return $this->inequalityOperator->less($this->get(), $x->get());
    }
}
