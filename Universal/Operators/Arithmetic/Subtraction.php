<?php
namespace Universal\Operators\Arithmetic;
use Universal\Operators as Operators;
use Universal\Operable as Operable;

abstract class Subtraction implements Operators\Binary
{
    public function subtract($x, $y)
    {
        return $this->apply($x, $y);
    }
}