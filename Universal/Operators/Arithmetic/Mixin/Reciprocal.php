<?php
namespace Universal\Operators\Arithmetic\Mixin;
use Universal\Operators\Arithmetic as Arithmetic;
use Universal\Operable as Operable;

trait Reciprocal
{
    private $reciprocalOperator = null;
    
    public function setReciprocalOperator(Arithmetic\Reciprocal $operator)
    {
        $this->reciprocalOperator = $operator;
    }
    
    public function reverse($x)
    {
        return $this->reciprocalOperator->reverse($x);
    }
    
    protected function reverseImmutable($x)
    {
        return $this->reciprocalOperator->reverse($x);
    }
}
