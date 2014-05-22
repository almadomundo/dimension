<?php
namespace Universal\Operable\Numeric\Integer;
use Universal\Operable\Comparable as Comparable;
use Universal\Operable\Measurable as Measurable;
use Universal\Operable\Numeric as Numeric;

class Number extends Numeric\Number
{
    use Comparable\Linear;
    use Measurable\Arithmetic;
    
    public function isValid($x)
    {
        return preg_match('/^[+-]?\d+$/', (string)$x);
    }
    
    protected function setDomainType($x) 
    {
        return (int)$x;
    }
}