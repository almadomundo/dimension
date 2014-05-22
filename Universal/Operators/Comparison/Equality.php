<?php
namespace Universal\Operators\Comparison;
use Universal\Operators as Operators;
use Universal\Operable as Operable;

abstract class Equality implements Operators\Binary
{
    public function equal(Operable\Comparable $x, Operable\Comparable $y)
    {
        return $this->apply($x->get(), $y->get());
    }
    
    public function notEqual(Operable\Comparable $x, Operable\Comparable $y)
    {
        return !$this->equal($x->get(), $y->get());
    }
}