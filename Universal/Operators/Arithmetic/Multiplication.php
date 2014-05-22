<?php
namespace Universal\Operators\Arithmetic;
use Universal\Operators as Operators;
use Universal\Operable as Operable;

abstract class Multiplication implements Operators\Binary
{
    public function product($x, $y)
    {
        return $this->apply($x, $y);
    }
}