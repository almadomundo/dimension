<?php
namespace Universal\Operable\Measurable\Mixin;
use Universal\Operators\Arithmetic as ArithmeticOperators;
use Universal\Operable as Operable;

trait Subtraction
{
    private $subtractionOperator;
    
    public function setSubtractionOperator(ArithmeticOperators\Subtraction $operator)
    {
        $this->subtractionOperator = $operator;
    }
    
    public function subtractWith(Operable\Operable $x)
    {
        $this->set($this->subtractionOperator->subtract($this->get(), $x->get()));
        return $this;
    }
}
