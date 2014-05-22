<?php
namespace Universal\Operators\Arithmetic\Mixin;
use Universal\Operators\Arithmetic as Arithmetic;
use Universal\Operable as Operable;

trait Subtraction
{
    private $subtractionOperator = null;
    
    public function setSubtractionOperator(Arithmetic\Subtraction $operator)
    {
        $this->subtractionOperator = $operator;
    }
    
    public function subtract($x, $y)
    {
        return $this->subtractionOperator->subtract($x, $y);
    }
    
    protected function subtractImmutable($x, $y)
    {
        return $this->subtractionOperator->subtract($x, $y);
    }
}
