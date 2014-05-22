<?php
namespace Universal\Operators\Numeric\Integer\Arithmetic;
use Universal\Operators\Arithmetic as Arithmetic;
use Universal\Operable as Operable;

class Field extends Arithmetic\Field
{
    public function divide($x, $y)
    {
        return $this->divideImmutable($x, $y);
    }
}