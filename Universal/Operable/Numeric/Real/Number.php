<?php
namespace Universal\Operable\Numeric\Real;
use Universal\Operable\Numeric as Numeric;

class Number extends Numeric\Number
{ 
    public function isValid($x)
    {
        return is_numeric($x);
    }
    
    protected function setDomainType($x) 
    {
        return (double)$x;
    }
}