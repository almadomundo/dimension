<?php
namespace Universal\Operators\Numeric\Integer\Arithmetic;
use Universal\Operators\Arithmetic as Arithmetic;
use Universal\Operable as Operable;

class Division extends Arithmetic\Division
{   
    public function apply($x, $y) 
    {
        return (int)($x / $y);
    }
}