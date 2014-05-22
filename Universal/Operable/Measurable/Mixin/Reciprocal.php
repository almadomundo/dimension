<?php
namespace Universal\Operable\Measurable\Mixin;
use Universal\Operators\Arithmetic as ArithmeticOperators;

trait Reciprocal
{
    private $reciprocalOperator;
    
    public function setReciprocalOperator(ArithmeticOperators\Reciprocal $operator)
    {
        $this->reciprocalOperator = $operator;
    }
    
    public function reverse()
    {
        $this->set($this->reciprocalOperator->reverse($this->get()));
        return $this;
    }
}
