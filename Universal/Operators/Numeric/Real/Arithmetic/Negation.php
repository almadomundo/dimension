<?php
namespace Universal\Operators\Numeric\Real\Arithmetic;
use Universal\Operators\Arithmetic as Arithmetic;
use Universal\Operable as Operable;

class Negation extends Arithmetic\Negation
{   
    public function apply($x) 
    {
        return -$x;
    }
}