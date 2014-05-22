<?php
namespace Universal\Operable\Measurable\Mixin;
use Universal\Operators\Arithmetic as ArithmeticOperators;
use Universal\Operable as Operable;

trait Summation
{
    private $summationOperator;
    
    public function setSummationOperator(ArithmeticOperators\Summation $operator)
    {
        $this->summationOperator = $operator;
    }
    
    public function sumWith(Operable\Operable $x)
    {
        $this->set($this->summationOperator->sum($this->get(), $x->get()));
        return $this;
    }
}
