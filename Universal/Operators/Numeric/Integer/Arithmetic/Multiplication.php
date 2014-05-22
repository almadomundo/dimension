<?php
namespace Universal\Operators\Numeric\Integer\Arithmetic;
use Universal\Operators\Arithmetic as Arithmetic;
use Universal\Operable as Operable;

class Multiplication extends Arithmetic\Multiplication
{   
    public function apply($x, $y) 
    {
        return $x * $y;
    }
}