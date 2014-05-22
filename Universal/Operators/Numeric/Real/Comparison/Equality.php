<?php
namespace Universal\Operators\Numeric\Real\Comparison;
use Universal\Operators\Comparison as Comparison;
use Universal\Operable as Operable;

class Equality extends Comparison\Equality
{   
    public function apply($x, $y) 
    {
        return $x == $y;
    }
}