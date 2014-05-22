<?php
namespace Universal\Operable\Measurable\Mixin;
use Universal\Operators\Arithmetic as ArithmeticOperators;
use Universal\Operable as Operable;

trait Multiplication
{
    private $multiplicationOperator;
    
    public function setMultiplicationOperator(ArithmeticOperators\Multiplication $operator)
    {
        $this->multiplicationOperator = $operator;
    }
    
    public function productWith(Operable\Operable $x)
    {
        $this->set($this->multiplicationOperator->product($this->get(), $x->get()));
        return $this;
    }
}
