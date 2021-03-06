<?php
namespace Universal\Operators\Numeric\Integer\Arithmetic;
use Universal\Operators\Arithmetic as Arithmetic;
use Universal\Operable as Operable;

class Subtraction extends Arithmetic\Subtraction
{   
    public function apply($x, $y) 
    {
        return $x - $y;
    }
}