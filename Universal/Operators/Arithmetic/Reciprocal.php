<?php
namespace Universal\Operators\Arithmetic;
use Universal\Operators as Operators;
use Universal\Operable as Operable;

abstract class Reciprocal implements Operators\Unary
{
    public function reverse($x)
    {
        return $this->apply($x);
    }
}