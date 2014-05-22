<?php
namespace Universal\Operators\Arithmetic\Mixin;
use Universal\Operators\Arithmetic as Arithmetic;
use Universal\Operable as Operable;

trait Summation
{
    private $summationOperator = null;
    
    public function setSummationOperator(Arithmetic\Summation $operator)
    {
        $this->summationOperator = $operator;
    }
    
    public function sum($x, $y)
    {
        return $this->summationOperator->sum($x, $y);
    }
    
    protected function sumImmutable($x, $y)
    {
        return $this->summationOperator->sum($x, $y);
    }
}
