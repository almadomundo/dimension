<?php
namespace Universal\Operable\Measurable\Mixin;
use Universal\Operators\Arithmetic as ArithmeticOperators;

trait Negation
{
    private $negationOperator;
    
    public function setNegationOperator(ArithmeticOperators\Negation $operator)
    {
        $this->negationOperator = $operator;
    }
    
    public function negate()
    {
        $this->set($this->negationOperator->negate($this->get()));
        return $this;
    }
}
