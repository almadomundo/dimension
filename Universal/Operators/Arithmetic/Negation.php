<?php
namespace Universal\Operators\Arithmetic;
use Universal\Operators as Operators;
use Universal\Operable as Operable;

abstract class Negation implements Operators\Unary
{
    public function negate($x)
    {
        return $this->apply($x);
    }
}