<?php
namespace Universal\Operators\Arithmetic\Mixin;
use Universal\Operators\Arithmetic as Arithmetic;
use Universal\Operable as Operable;

trait Negation
{
    private $negationOperator = null;
    
    public function setNegationOperator(Arithmetic\Negation $operator)
    {
        $this->negationOperator = $operator;
    }
    
    public function negate($x)
    {
        return $this->negationOperator->negate($x);
    }
    
    protected function negateImmutable($x)
    {
        return $this->negationOperator->negate($x);
    }
}
