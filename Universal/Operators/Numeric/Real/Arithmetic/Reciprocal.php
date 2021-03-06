<?php
namespace Universal\Operators\Numeric\Real\Arithmetic;
use Universal\Operators\Arithmetic as Arithmetic;
use Universal\Operable as Operable;

class Reciprocal extends Arithmetic\Reciprocal
{   
    public function apply($x) 
    {
        return 1 / $x;
    }
}