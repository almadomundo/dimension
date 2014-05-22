<?php
namespace Universal\Operators\Arithmetic\Mixin;
use Universal\Operators\Arithmetic as Arithmetic;
use Universal\Operable as Operable;

trait Multiplication
{
    private $multiplicationOperator = null;
    
    public function setMultiplicationOperator(Arithmetic\Multiplication $operator)
    {
        $this->multiplicationOperator = $operator;
    }
    
    public function product($x, $y)
    {
        return $this->multiplicationOperator->product($x, $y);
    }
    
    protected function productImmutable($x, $y)
    {
        return $this->multiplicationOperator->product($x, $y);
    }
}
