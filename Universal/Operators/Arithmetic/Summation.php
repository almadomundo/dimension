<?php
namespace Universal\Operators\Arithmetic;
use Universal\Operators as Operators;
use Universal\Operable as Operable;

abstract class Summation implements Operators\Binary
{
    public function sum($x, $y)
    {
        return $this->apply($x, $y);
    }
}