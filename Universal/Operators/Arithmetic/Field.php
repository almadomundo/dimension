<?php
namespace Universal\Operators\Arithmetic;
use Universal\Operators\Arithmetic\Mixin as Mixin;
use Universal\Operable as Operable;

abstract class Field 
{
    use Mixin\Summation;
    use Mixin\Multiplication;
    use Mixin\Subtraction;
    use Mixin\Division;
    use Mixin\Negation;
    use Mixin\Reciprocal;
    
    public function subtract($x, $y)
    {
        return $this->sum($x, $this->negate($y));
    }
    
    public function divide($x, $y)
    {
        return $this->product($x, $this->reverse($y));
    }
}