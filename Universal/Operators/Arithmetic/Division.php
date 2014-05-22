<?php
namespace Universal\Operators\Arithmetic;
use Universal\Operators as Operators;
use Universal\Operable\Numeric as Numeric;

abstract class Division implements Operators\Binary
{
    public function divide(Numeric\Enumerable $x, Numeric\Enumerable $y, Numeric\Enumerable $z=null)
    {
        if(isset($z))
        {
            return $this->divideZeroBound($x, $y, $z);
        }
        return $this->apply($x->get(), $y->get());
    }
    
    protected function divideZeroBound(Numeric\Enumerable $x, Numeric\Enumerable $y, Numeric\Enumerable $z)
    {
        if($y->notEqualTo($z))
        {
            return $this->apply($x->get(), $y->get());
        }
        throw new Operators\Arithmetic\Exception\Division('Division by zero');
    }
}