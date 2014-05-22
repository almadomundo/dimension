<?php
namespace Universal\Operators\Comparison;
use Universal\Operators as Operators;
use Universal\Operable as Operable;

abstract class Inequality implements Operators\Binary
{
    public function greater($x, $y)
    {
        return $this->apply($x, $y);
    }
    
    public function less($x, $y)
    {
        return !$this->greater($x, $y);
    }
}