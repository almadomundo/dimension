<?php
namespace Universal\Operators;
use Universal\Operable as Operable;

interface Binary extends Operator
{    
    public function apply($x, $y);
}