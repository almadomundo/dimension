<?php
namespace Universal\Operators\Comparison\Mixin;
use Universal\Operators\Comparison as Comparison;

trait Inequality
{
    private $inequalityOperator = null;
    
    public function setInequalityOperator(Comparison\Inequality $operator)
    {
        $this->inequalityOperator = $operator;
    }
    
    public function greater($x, $y)
    {
        return $this->inequalityOperator->greater($x, $y);
    }
    
    public function less($x, $y)
    {
        return $this->inequalityOperator->less($x, $y);
    }
    
    protected function greaterImmutable($x, $y)
    {
        return $this->inequalityOperator->greater($x, $y);
    }
    
    protected function lessImmutable($x, $y)
    {
        return $this->inequalityOperator->less($x, $y);
    }
}

