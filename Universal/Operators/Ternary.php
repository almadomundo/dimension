<?php
namespace Universal\Operators;

interface Ternary extends Operator
{    
    public function apply($x, $y, $z);
}