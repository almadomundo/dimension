<?php
namespace Universal\Operators\Numeric\Integer\Arithmetic;
use Universal\Operators\Arithmetic as Arithmetic;
use Universal\Operable as Operable;

class Summation extends Arithmetic\Summation
{   
    public function apply($x, $y) 
    {
        return $x + $y;
    }
}